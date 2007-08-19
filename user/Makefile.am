# -*- Makefile -*-

AM_CFLAGS         = $(AC_CFLAGS) -I$(top_srcdir)/include
LDADD             = $(AC_LIBS)
CLEANFILES        = rplctl

bin_PROGRAMS      = ttyreplay
sbin_PROGRAMS     = rplctl rpld
EXTRA_PROGRAMS    = ptmx
ttyreplay_SOURCES = lib.c replay.c pctrl.c
ttyreplay_LDADD   = -lHX -lpthread $(ttyreplay_AC_LIBS)
ptmx_SOURCES      = ptmx.c
rpld_SOURCES      = infod.c lib.c rdsh.c rplctl.c rpld.c
rpld_LDADD        = -lHX -lpthread $(rpld_AC_LIBS)

rplctl: rpld
	[ ! -e rplctl ] && ln -s rpld rplctl;
