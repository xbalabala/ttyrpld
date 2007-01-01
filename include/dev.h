/*=============================================================================
ttyrpld - TTY replay daemon
include/dev.h - Supply file for dev_t encoding
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
#ifndef RPL_DEV_H
#define RPL_DEV_H 1

#define K26_MINORBITS 20
#define K26_MINORMASK 0xFFFFF
#define K26_MAJOR(dev) (unsigned long)((dev) >> K26_MINORBITS)
#define K26_MINOR(dev) (unsigned long)((dev) & K26_MINORMASK)
#define K26_MKDEV(major, minor) \
    (((major) << K26_MINORBITS) | ((minor) & K26_MINORMASK))
#define KD26_PARTS(dev) K26_MAJOR(dev), K26_MINOR(dev)

/* FreeBSD and OpenBSD
 * Scheme is:
 *   bits  0 - 7      (8 bits)    minor, lower part
 *   bits  8 - 15     (8 bits)    major
 *   bits 16 - 31    (16 bits)    minor, upper part
 */
#if defined(__FreeBSD__) || defined(__OpenBSD__)
#    define COMPAT_MAJOR(dev) \
        (unsigned long)(((dev) & 0xFF00) >> 8)
#    define COMPAT_MINOR(dev) \
        (unsigned long)(((dev) & 0xFF) | (((dev) & 0xFFFF0000) >> 8))
#    define COMPAT_MKDEV(major, minor) \
        (unsigned long)((((major) & 0xFF) << 8) | ((minor) & 0xFF) | \
        (((minor) & 0xFFFF00) << 8))

/* Glibc-Linux and NetBSD
 * Scheme is:
 *   bits  0 -  7     (8 bits)    minor, lower part
 *   bits  8 - 19    (12 bits)    major
 *   bits 20 - 31    (12 bits)    minor, upper part
 */
#else
#    define COMPAT_MAJOR(dev) \
        (unsigned long)(((dev) & 0xFFF00) >> 8)
#    define COMPAT_MINOR(dev) \
        (unsigned long)(((dev) & 0xFF) | (((dev) & 0xFFF00000) >> 12))
#    define COMPAT_MKDEV(major, minor) \
        (unsigned long)(((minor) & 0xFF) | (((minor) & 0xFFF00) << 12) | \
        (((major) & 0xFFF) << 8))
#endif

#endif // RPL_DEV_H

//=============================================================================
