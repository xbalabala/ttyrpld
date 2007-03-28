# -*- Makefile -*-

AM_CFLAGS         = $(AC_CFLAGS) -I$(top_srcdir)/include

bin_PROGRAMS      = ttyreplay
sbin_PROGRAMS     = rplctl rpld
EXTRA_PROGRAMS    = ptmx
ttyreplay_SOURCES = lib.c replay.c pctrl.c
ttyreplay_LDFLAGS = -lHX -lpthread
ptmx_SOURCES      = ptmx.c
rpld_SOURCES      = infod.c lib.c rdsh.c rplctl.c rpld.c
rpld_LDFLAGS      = -lHX -lpthread

rplctl: rpld
	ln -s $< $@;
