/*=============================================================================
ttyrpld - TTY replay daemon
include/rdsh.h - Shared definitions for RPLD/INFO/RPLINFO
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
#ifndef TTYRPLD_RDSH_H
#define TTYRPLD_RDSH_H 1

#include <sys/types.h>
#include <pthread.h>
#include "rpl_stdint.h"
#include <libHX.h>
#if !defined(_LIBHX_H) || _LIBHX_H < 20060225
#    error ----------------------------------------
#    error You need a newer version of libHX
#    error (at least 1.8.0)
#    error http:/jengelh.hopto.org/coding/libHX.php
#    error ----------------------------------------
#endif

#ifdef __cplusplus
extern "C" {
#endif

#define RECEIVE_FULL(fd, ptr, sz) \
    (recv((fd), (ptr), (sz), MSG_WAITALL) == (sz))

struct GOptmap_t {
    int syslog, verbose;
    long user_id;
    const char *infod_port, *ofmt;
};

struct Statmap_t {
    unsigned long init, open, read, write, ioctl, close, deinit, badpack;
    unsigned long long in, out;
};

struct tty {
    uint32_t dev;
    uid_t uid;
    int fd, status;
    unsigned long in, out;
    char *log, *sdev, *full_dev;
};

enum {
    IFP_NONE = 0x3F,
    IFP_DEFAULT,
    IFP_ACTIVATE,
    IFP_DEACTIVATE,
    IFP_DEACTIVSES,
    IFP_REMOVE,
    IFP_GETINFO,
    IFP_GETINFO_T,
    IFP_ZERO,
};

// infod.c
extern void infod_init(void);
extern void *infod_main(void *);

// rplctl.c
extern int rplctl_main(int, const char **);

// rdsh.c
extern pthread_mutex_t Ttys_lock;
extern const char *Device_dirs[];
extern struct HXbtree *Ttys;
extern struct Statmap_t Stats;
extern struct GOptmap_t GOpt;

extern struct tty *get_tty(uint32_t, int);
extern void log_close(struct tty *);
extern void notify(int, const char *, ...);
extern ssize_t send_wait(int, const void *, size_t, int);

#ifdef __cplusplus
} // extern "C"
#endif

#endif // TTYRPLD_RDSH_H

//=============================================================================
