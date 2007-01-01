/*=============================================================================
ttyrpld - TTY replay daemon
include/rpl_packet.h - Packet definitions for RPLD and RPLDEV
  Copyright © Jan Engelhardt <jengelh [at] gmx de>, 2004 - 2007
  Copyright © Nguyen Anh Quynh <aquynh [at] gmail com>, 2005

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
#ifndef RPL_PACKET_H
#define RPL_PACKET_H 1

#define __PACKED __attribute__((packed))

#if defined(__linux__) && defined(__KERNEL__)
#    include <linux/time.h>
#    include <linux/types.h>
#elif (defined(__FreeBSD__) || defined(__OpenBSD__)) && defined(_KERNEL)
#    include <sys/time.h>
#    include <sys/types.h>
#else // userspace
#    include <sys/time.h>
#    include "rpl_stdint.h"
#endif

enum {
    EVT_NONE       = 0x00,
    EVT_OPEN       = 0x01,
    EVT_READ       = 0x02,
    EVT_WRITE      = 0x03,
    EVT_IOCTL      = 0x04,
    EVT_CLOSE      = 0x05,
    EVT_MAGIC      = 0x4A,
    EVT_INIT       = 0x69,
    EVT_DEINIT     = 0x64,
    EVT_ID_PROG    = 0xF0,
    EVT_ID_DEVPATH = 0xF1,
    EVT_ID_TIME    = 0xF2,
    EVT_ID_USER    = 0xF3,
    EVT_max,
    MAGIC_SIG      = 0xEE,
};

struct rpldev_packet {
    uint32_t dev;
    uint16_t size;
    uint8_t event, magic;
    struct timeval time;
} __PACKED;

struct rpldsk_packet {
    uint16_t size;
    uint8_t event, magic;
    struct timeval time;
} __PACKED;

#endif // RPL_PACKET_H

//=============================================================================
