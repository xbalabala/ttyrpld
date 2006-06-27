# -*- Makefile -*-

V        := 0
DEBUG    := 0
PREFIX   := /usr/local

cc-option := $(shell if $(CC) $(CFLAGS) $(1) -S -o /dev/null -xc /dev/null \
             >/dev/null 2>/dev/null; then echo -en "$(1)"; else echo "$(2)"; fi;)
check_gcc := $(call cc-option,$(1),$(2))

CC       := gcc
CXX      := g++
CFLAGS   := -D_LARGEFILE_SOURCE=1 -D_LARGE_FILES -D_FILE_OFFSET_BITS=64 \
            -D_REENTRANT -DPIC -Iinclude -Wall -Waggregate-return \
            -Wmissing-declarations -Wmissing-prototypes -Wredundant-decls \
            -Wshadow -Wstrict-prototypes -Winline -fPIC -pipe
CXXFLAGS := -D_LARGEFILE_SOURCE=1 -D_LARGE_FILES -D_FILE_OFFSET_BITS=64 \
            -D_REENTRANT -DPIC -Iinclude -Wall -Wmissing-prototypes \
            -Wredundant-decls -Wstrict-prototypes -fPIC -pipe
AS       := gcc # as
ASFLAGS  :=
LD       := gcc # ld
LDXX     := g++ # ld
LDFLAGS  :=
SOFLAGS  := -shared
AR       := ar
ARFLAGS  :=

ifeq (${DEBUG},1)
  CFLAGS   += -ggdb3
  CXXFLAGS += -ggdb3
  ASFLAGS  += -ggdb3
  STRIP    := true
else
  CFLAGS   += -O2 -finline-functions -fomit-frame-pointer
  CXXFLAGS += -O2 -finline-functions -fomit-frame-pointer
  ASFLAGS  += -O2 -finline-functions
  STRIP    := strip
endif
ifeq (${PROF},1)
  CFLAGS   += -pg -fprofile-arcs -ftest-coverage -fno-omit-frame-pointer
  CXXFLAGS += -pg -fprofile-arcs -ftest-coverage -fno-omit-frame-pointer
  ASFLAGS  += -pg -fprofile-arcs -ftest-coverage
  LDFLAGS  += -pg -fprofile-arcs -ftest-coverage
  STRIP    := true
endif

ifeq (${V},0)
  Q := @
  VECHO_CC  = ${Q}echo "  CC    " $@
  VECHO_CXX = ${Q}echo "  CXX   " $@
  VECHO_AS  = ${Q}echo "  AS    " $@
  VECHO_LD  = ${Q}echo "  LD    " $@
  VECHO_AR  = ${Q}echo "  AR    " $@
endif

# Enable unit-at-a-time mode when possible.
# It shrinks the binary size considerably.
CFLAGS += $(call check_gcc,-funit-at-a-time,)

#==============================================================================
