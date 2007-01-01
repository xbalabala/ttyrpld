/*=============================================================================
ttyrpld - TTY replay daemon
include/rpl_endian.h
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
#ifndef RPL_ENDIAN_H
#define RPL_ENDIAN_H 1

#if defined(__FreeBSD__) || defined(__NetBSD__)
#    include <sys/endian.h>
#    if _BYTE_ORDER == _BIG_ENDIAN
#        define SWAB(ptr, sz) swab_be((ptr), (sz))
#        define SWAB1(ptr) swab_be((ptr), sizeof(*(ptr)))
#    else
#        define SWAB(ptr, sz)
#        define SWAB1(ptr)
#    endif
#elif defined(__OpenBSD__)
#    include <machine/endian.h>
#    if BYTE_ORDER == BIG_ENDIAN
#        define SWAB(ptr, sz) swab_be((ptr), (sz))
#        define SWAB1(ptr) swab_be((ptr), sizeof(*(ptr)))
#    else
#        define SWAB(ptr, sz)
#        define SWAB1(ptr)
#    endif
#else
#    include <endian.h>
#    if __BYTE_ORDER == __BIG_ENDIAN
#        define SWAB(ptr, sz) swab_be((ptr), (sz))
#        define SWAB1(ptr) swab_be((ptr), sizeof(*(ptr)))
#    else
#        define SWAB(ptr, sz)
#        define SWAB1(ptr)
#    endif
#endif

#endif // RPL_ENDIAN_H

//=============================================================================
