/*=============================================================================
ttyrpld - TTY replay daemon
include/global.h - Shared definitions for user-space apps
  Copyright © Jan Engelhardt <jengelh [at] gmx de>, 2004 - 2006

  Redistribution and use in source and binary forms, with or without
  modification, are permitted provided that the following conditions are met:

  1. Redistributions of source code must retain the above copyright notice,
     this list of conditions and the following disclaimer.
  2. Redistributions in binary form must reproduce the above copyright notice,
     this list of conditions and the following disclaimer in the documentation
     and/or other materials provided with the distribution.
  3. Redistributions of modified code that are made available only in binary
     form require sending a description to the ttyrpld project maintainer on
     what has been changed.
  4. Neither the names of the above-listed copyright holders nor the names of
     any contributors may be used to endorse or promote products derived from
     this software without specific prior written permission.

  NO WARRANTY. THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND
  CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT
  NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTIBILITY AND FITNESS FOR A
  PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS OR
  CONTRIBUTORS BE LIABLE FOR SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
  ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.
=============================================================================*/
#ifndef TTYRPLD_LIB_H
#define TTYRPLD_LIB_H 1

#include <sys/types.h>
#include <locale.h>
#include <libintl.h>
#include "rpl_endian.h"
#include "rpl_stdint.h"

#ifdef __cplusplus
extern "C" {
#endif

#ifndef O_BINARY
#    define O_BINARY 0
#endif
#define _(s) gettext(s)

#define MAXFNLEN 256
#define TTYRPLD_VERSION "2.17"
#if defined(__FreeBSD__) || defined(__OpenBSD__)
#    define D_IOCTL(fd, cmd, eax) (ioctl((fd), (cmd), *(eax)) == 0)
#else
#    define D_IOCTL(fd, cmd, eax) ((*(eax) = ioctl((fd), (cmd))) >= 0)
#endif

struct pctrl_info;

extern void load_locale(const char *);
extern ssize_t read_wait(int, void *, size_t, const struct pctrl_info *);
extern off_t G_skip(int, off_t, int);
extern void swab_be(void *, size_t);

static inline unsigned int min_uint(unsigned int a, unsigned int b) {
    return (a < b) ? a : b;
}

#ifdef __cplusplus
} // extern "C"
#endif

#endif // TTYRPLD_LIB_H

//=============================================================================
