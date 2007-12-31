# -*- Makefile -*-

AM_CFLAGS         = $(regular_CFLAGS) -I$(top_srcdir)/include
LDADD             = $(regular_LIBS)
CLEANFILES        = rplctl

bin_PROGRAMS      = rplcvt ttyreplay
sbin_PROGRAMS     = rplctl rpld
EXTRA_PROGRAMS    = ptmx
ttyreplay_SOURCES = lib.c replay.c pctrl.c
ttyreplay_LDADD   = -lHX -lpthread $(ttyreplay_LIBS)
ptmx_SOURCES      = ptmx.c
rplcvt_SOURCES    = rplcvt.c lib.c
rplcvt_LDADD      = -lHX
rpld_SOURCES      = infod.c lib.c rdsh.c rplctl.c rpld.c
rpld_LDADD        = -lHX -lpthread $(rpld_LIBS)

rplctl: rpld
	if [ ! -e rplctl ]; then ln -s rpld rplctl; fi;
