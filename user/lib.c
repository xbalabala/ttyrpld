/*=============================================================================
ttyrpld - TTY replay daemon
user/lib.c - Shared functions for user-space apps
  Copyright © Jan Engelhardt <jengelh [at] gmx de>, 2004 - 2007
  Copyright © Nguyen Anh Quynh <aquynh [at] gmail com>, 2005
  -- License restrictions apply (LGPL v2.1)

  This file is part of ttyrpld.
  ttyrpld is free software; you can redistribute it and/or modify it
  under the terms of the GNU Lesser General Public License as published
  by the Free Software Foundation; however ONLY version 2 of the License.

  ttyrpld is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
  Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public
  License along with this program kit; if not, write to:
  Free Software Foundation, Inc., 51 Franklin St, Fifth Floor,
  Boston, MA  02110-1301  USA

  -- For details, see the file named "LICENSE.LGPL2"
=============================================================================*/
#include <sys/ioctl.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <errno.h>
#include <sched.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <libHX.h>
#include "dev.h"
#include "lib.h"
#include "pctrl.h"
#include "rpl_ioctl.h"

//-----------------------------------------------------------------------------
void load_locale(const char *exe) {
    struct stat sb;
    int ok = 0;
    setlocale(LC_ALL, "");

    if(strchr(exe, '/') != NULL) {
        hmc_t *base = hmc_sinit(exe), *de;
        hmc_trunc(&base, HX_strrcspn(base, "/"));
        hmc_strcat(&base, "/locale");
        de = hmc_dup(base);
        hmc_strcat(&de, "/de/LC_MESSAGES/ttyrpld.mo");
        if(stat(de, &sb) == 0) {
            bindtextdomain("ttyrpld", base);
            ++ok;
        }
        hmc_free(base);
        hmc_free(de);
    }

    if(!ok) {
        if(stat("locale/de/LC_MESSAGES/ttyrpld.mo", &sb) == 0)
            bindtextdomain("ttyrpld", ".");
        else if(stat("/usr/local/share/locale/de/LC_MESSAGES/ttyrpld.mo", &sb) == 0)
            bindtextdomain("ttyrpld", "/usr/local/share/locale");
        else if(stat("/usr/share/locale/de/LC_MESSAGES/ttyrpld.mo", &sb) == 0)
            bindtextdomain("ttyrpld", "/usr/share/locale");
    }

    textdomain("ttyrpld");
    return;
}

ssize_t read_wait(int fd, void *buf, size_t count,
 const struct pctrl_info *ps)
{
    /* A wrapper for read() which guarantees that all bytes requested will
    be in BUF after read_wait() returns. (Except if there is an error.)
    Note that it will retry to read when it hits EOF, so only use this function
    on files which are still being written to!
    Cannot use select() here, because it returns "ready" for end-of-file,
    which does not help us more than simple read()s. */
    size_t rem = count;

    while(rem > 0) {
        ssize_t eax = read(fd, buf, rem);
        if(eax < 0)
            return -errno;
        if(eax == rem)
            break;
        buf += eax;
        rem -= eax;
        if(ps->brk)
            return count - rem;
        usleep(10000);
    }
    return count;
}

off_t G_skip(int fd, off_t offset, int do_wait) {
    /* For files (and stuff) that can not be seeked in, use a slurping method
    to get to the wanted position. This only works for forward offsets. */
    off_t seekable = lseek(fd, 0, SEEK_CUR);
    size_t rem = offset;
    char buf[4096];

#if (defined(__FreeBSD__) || defined(__OpenBSD__) || defined(__NetBSD__))
    size_t eax = 0;
    if(D_IOCTL(fd, RPL_IOC_IDENTIFY, &eax) && eax == 0xC0FFEE) {
        /* BSD does not have lseek() for device files.
        And its return value scheme is also limited. */
        eax = offset;
        D_IOCTL(fd, RPL_IOC_SEEK, &eax);
        return eax;
    }
#endif
    if(seekable != -1)
        return lseek(fd, offset, SEEK_CUR);

    if(do_wait) {
        while(rem > 0) {
            ssize_t ebx = read(fd, buf, min_uint(sizeof(buf), rem));
            if(ebx < 0)
                return -1;
            if(ebx == rem)
                return 0;
            rem -= ebx;
            usleep(10000);
        }
        return 0;
    }

    // non wait method
    while(rem > 0) {
        ssize_t ebx = read(fd, buf, min_uint(sizeof(buf), rem));
        if(ebx <= 0)
            return -1;
        if(ebx == rem)
            return 0;
        rem -= ebx;
        usleep(10000);
    }

    return 0;
}

void swab_be(void *srcp, size_t count) {
    char *movp = srcp, x;

    while(count) {
        x = *movp++;
        movp[0] = movp[1];
        *++movp = x;
        count -= 2;
    }

    return;
}

//=============================================================================
