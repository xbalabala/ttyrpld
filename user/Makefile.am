# -*- Makefile -*-

AM_CFLAGS         = $(AC_CFLAGS) -I$(top_srcdir)/include
AM_LDFLAGS        = $(AC_LDLIBS)

bin_PROGRAMS      = ttyreplay
sbin_PROGRAMS     = rplctl rpld
EXTRA_PROGRAMS    = ptmx
ttyreplay_SOURCES = lib.c replay.c pctrl.c
ttyreplay_LDFLAGS = -lHX -lpthread $(ttyreplay_AC_LDLIBS)
ptmx_SOURCES      = ptmx.c
rpld_SOURCES      = infod.c lib.c rdsh.c rplctl.c rpld.c
rpld_LDFLAGS      = -lHX -lpthread $(rpld_AC_LDLIBS)

rplctl: rpld
	ln -fs rpld rplctl;
