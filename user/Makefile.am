# -*- Makefile -*-

AM_CFLAGS         = ${regular_CFLAGS} -I${top_srcdir}/include ${libHX_CFLAGS}
LDADD             = ${regular_LIBS}
CLEANFILES        = rplctl${EXEEXT}

bin_PROGRAMS      = rplcvt ttyreplay
sbin_PROGRAMS     = rpld
sbin_SCRIPTS      = rplctl${EXEEXT}
EXTRA_PROGRAMS    = ptmx
ttyreplay_SOURCES = lib.c replay.c pctrl.c
ttyreplay_LDADD   = ${libHX_LIBS} -lpthread ${ttyreplay_LIBS}
ptmx_SOURCES      = ptmx.c
rplcvt_SOURCES    = rplcvt.c lib.c
rplcvt_LDADD      = ${libHX_LIBS}
rpld_SOURCES      = infod.c lib.c rdsh.c rplctl.c rpld.c
rpld_LDADD        = ${libHX_LIBS} -lpthread ${rpld_LIBS}

rplctl${EXEEXT}: rpld${EXEEXT}
	if [ ! -e rplctl${EXEEXT} ]; then ln -s rpld${EXEEXT} rplctl${EXEEXT}; fi;
