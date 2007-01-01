/*=============================================================================
ttyrpld - TTY replay daemon
include/rpl_ioctl.h - IOCTL numbers for RPLDEV
  Copyright © Jan Engelhardt <jengelh [at] gmx de>, 2004 - 2007

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
#ifndef RPL_IOCTL_H
#define RPL_IOCTL_H 1

#if defined(__linux__) && defined(__KERNEL__)
#    include <linux/ioctl.h>
#    include <linux/types.h>
#elif defined(__FreeBSD__) && defined(_KERNEL)
#    include <sys/ioccom.h>
#else // userspace
#    include <sys/types.h>
#    include <sys/ioctl.h>
#endif

enum {
    /* Some weird *BSD scheme makes me use IOWR, otherwise readin the pointer
    within kernel space does not seem to work. */
    RPL_IOC_MAGIC      = 0xB7,
    RPL_IOC_GETBUFSIZE = _IOWR(RPL_IOC_MAGIC, 1, size_t),
    RPL_IOC_GETRAVAIL  = _IOWR(RPL_IOC_MAGIC, 2, size_t),
    RPL_IOC_GETWAVAIL  = _IOWR(RPL_IOC_MAGIC, 3, size_t),
    RPL_IOC_IDENTIFY   = _IOWR(RPL_IOC_MAGIC, 4, size_t),
    RPL_IOC_SEEK       = _IOWR(RPL_IOC_MAGIC, 5, size_t),
    RPL_IOC_FLUSH      = _IOWR(RPL_IOC_MAGIC, 6, size_t),
    RPL_IOC__MAXNUM    = _IOWR(RPL_IOC_MAGIC, 7, size_t),
};

#endif // RPL_IOCTL_H

//=============================================================================
