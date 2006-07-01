/*=============================================================================
ttyrpld - TTY replay daemon
user/rplctl.c - INFOD client
  Copyright © Jan Engelhardt <jengelh [at] gmx de>, 2004 - 2006
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
#include <sys/types.h>
#include <sys/select.h>
#include <sys/socket.h>
#include <sys/stat.h>
#include <sys/un.h>
#include <errno.h>
#include <sched.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#include <libHX.h>
#include "dev.h"
#include "lib.h"
#include "rdsh.h"

#define IFP_GETINFO_A (Opt.parseable ? IFP_GETINFO : IFP_GETINFO_T)

// Function prototypes
static uint32_t getdev(const char *);
static void send_int(int, unsigned char, uint32_t);
static int unix_client(const char *);

static int get_options(int *, const char ***);
static void getopt_proc(const struct HXoptcb *);

// Global variables
static struct {
    int do_listing, parseable;
} Opt = {
    .do_listing = 0, // -L
    .parseable  = 0, // -t
};
static int Sockfd = -1, Got_arg = 0;

//-----------------------------------------------------------------------------
int rplctl_main(int argc, const char **argv) {
    if((Sockfd = unix_client(GOpt.infod_port)) < 0) {
        fprintf(stderr, _("Connection to %s failed: %s\n"),
         GOpt.infod_port, strerror(errno));
        return EXIT_FAILURE;
    }

    if(get_options(&argc, &argv) <= 0)
        return EXIT_FAILURE;

    if(Got_arg == 0) {
        if(!Opt.parseable)
            printf("A %-7s %13s %9s %s\n", _("TTY"), _("BYTES IN"),
             _("OUT"), _("FILENAME"));
        send_int(Sockfd, IFP_GETINFO_A, 0);
    }

    send_int(Sockfd, IFP_NONE, 0);
    close(Sockfd);
    return EXIT_SUCCESS;
}

//-----------------------------------------------------------------------------
static uint32_t getdev(const char *s) {
    uint32_t major, minor;
    int found = 0;

    if(s == NULL) return 0;
    if(*s == '=') {
        /* The user can specify a major-minor number combination
        in form of "=MAJOR,MINOR". */
        char *sdup = HX_strdup(s), *p = sdup, *minor_str;
        if((minor_str = strpbrk(p + 1, ",.:")) == NULL || p + 1 == minor_str) {
            fprintf(stderr, _("getdev(): Incorrect device number specification"
             " \"%s\", should be \"=%%d:%%d\"\n"), s);
            free(sdup);
            return 0;
        }
        *minor_str++ = '\0';
        major = strtoul(++p, NULL, 0);
        minor = strtoul(minor_str, NULL, 0);
        free(sdup);
    } else {
        /* ... or a pathname specification to a device node whose rdev number
        will be extracted. Note that we do not check for S_ISCHR or S_ISBLK,
        so passing /dev/hda here will effectively mean /dev/ttyp0. */
        const char *dirs[] = {"", "/dev/", NULL}, **dirp = dirs;
        struct stat sb;

        while(*dirp != NULL) {
            char buf[MAXFNLEN];
            snprintf(buf, MAXFNLEN, "%s/%s", *dirp, s);
            if(stat(buf, &sb) == 0) {
                ++found;
                break;
            }
            if(errno != ENOENT) {
                fprintf(stderr, _("Cannot stat %s: %s\n"),
                 buf, strerror(errno));
                return 0;
            }
            ++dirp;
        }
        if(!found) {
            fprintf(stderr, _("Cannot find %s\n"), s);
            return 0;
        }

        major = COMPAT_MAJOR(sb.st_rdev);
        minor = COMPAT_MINOR(sb.st_rdev);
    }

    return K26_MKDEV(major, minor);
}

static void read_reply(int fd) {
    char buf[4096];

    while(1) {
        uint32_t reply_size = 0;
        fd_set rd;

        recv(fd, &reply_size, sizeof(uint32_t), MSG_WAITALL);
        SWAB1(&reply_size);
        if(reply_size == 0)
            break;

        while(reply_size > 0) {
            FD_ZERO(&rd);
            FD_SET(fd, &rd);
            select(fd + 1, &rd, NULL, NULL, NULL);
            if(FD_ISSET(fd, &rd)) {
                ssize_t ret = recv(fd, buf,
                 min_uint(sizeof(buf), reply_size), 0);
                write(STDOUT_FILENO, buf, ret);
                reply_size -= ret;
            }
        }
    }
    return;
}

static void send_int(int fd, unsigned char req, uint32_t dev) {
    send_wait(fd, &req, sizeof(unsigned char), 0);
    SWAB1(&dev);
    send_wait(fd, &dev, sizeof(uint32_t), 0);
    read_reply(fd);
    return;
}

static int unix_client(const char *port) {
    struct sockaddr_un sk;
    int fd = -1;

    sk.sun_family = AF_UNIX;
    strncpy(sk.sun_path, port, sizeof(sk.sun_path));
    sk.sun_path[sizeof(sk.sun_path) - 1] = '\0';

    if((fd = socket(AF_UNIX, SOCK_STREAM, 0)) < 0 ||
     connect(fd, (struct sockaddr *)&sk, sizeof(struct sockaddr_un)) < 0)
        return -1;

    return fd;
}

//-----------------------------------------------------------------------------
static int get_options(int *argc, const char ***argv) {
    struct HXoption options_table[] = {
        {.sh = 'A', HXTYPE_STRING, .cb = getopt_proc,
         .help = _("Capture data from tty"), _("tty")},
        {.sh = 'D', HXTYPE_STRING, .cb = getopt_proc,
         .help = _("Ignore data from tty"), _("tty")},
        {.sh = 'L', HXTYPE_STRING | HXOPT_OPTIONAL, .cb = getopt_proc,
         .help = _("Explicitly request info about all [or given] ttys"),
         .htyp = _("tty")},
        {.sh = 'S', HXTYPE_STRING, .cb = getopt_proc,
         .help = _("Ignore data from tty until next tty session"), _("tty")},
        {.sh = 'X', HXTYPE_STRING, .cb = getopt_proc,
         .help = _("Close the tty's logfile"), _("tty")},
        {.sh = 'Z', HXTYPE_STRING | HXOPT_OPTIONAL, .cb = getopt_proc,
         .help = _("Zero all packet and byte counters"), .htyp = _("tty")},
        {.sh = 'f', HXTYPE_STRING, .ptr = &GOpt.infod_port,
         .help = _("Path to the INFOD socket"), .htyp = _("file")},
        {.sh = 't', HXTYPE_NONE, .ptr = &Opt.parseable,
         .help = _("Generate grepable output")},
        HXOPT_AUTOHELP,
        HXOPT_TABLEEND,
    };

    return HX_getopt(options_table, argc, argv, HXOPT_USAGEONERR);
}

static void getopt_proc(const struct HXoptcb *cbi) {
    static const int mapping[] = {
        ['A'] = IFP_ACTIVATE,
        ['D'] = IFP_DEACTIVATE,
        ['S'] = IFP_DEACTIVSES,
        ['X'] = IFP_REMOVE,
        ['Z'] = IFP_ZERO,
    };
    switch(cbi->tsh) {
        case 'A':
        case 'D':
        case 'S':
        case 'X': {
            uint32_t dev;
            ++Got_arg;
            if((dev = getdev(cbi->s)) != 0)
                send_int(Sockfd, mapping[(int)cbi->tsh], dev);
            break;
        }
        case 'L': {
            uint32_t dev = (cbi->s != NULL) ? getdev(cbi->s) : 0;
            ++Got_arg;
            send_int(Sockfd, IFP_GETINFO_A, dev);
            break;
        }
        case 'Z': {
            uint32_t dev = (cbi->s != NULL) ? getdev(cbi->s) : 0;
            ++Got_arg;
            send_int(Sockfd, mapping[(int)cbi->tsh], dev);
            break;
        }
    }
    return;
}

//=============================================================================