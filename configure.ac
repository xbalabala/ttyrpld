
AC_INIT(ttyrpld, 2.19)
AC_CONFIG_HEADERS(config.h)
AM_INIT_AUTOMAKE
AC_PROG_CC
AM_PROG_CC_C_O

AC_HAVE_HEADERS(alloca.h)
AC_CHECK_LIB([rt], [nanosleep], [ttyreplay_AC_LIBS="$ttyreplay_AC_LIBS -lrt"])
AC_CHECK_LIB([socket], [accept], [rpld_AC_LIBS="$rpld_AC_LIBS -lsocket -lnsl"])
AC_CHECK_LIB([intl], [gettext], [AC_LIBS="$AC_LIBS -lintl"])
AC_CHECK_HEADER([libHX.h], [],
	AC_MSG_ERROR([Need at least libHX 1.10 (http://jengelh.hopto.org/p/libHX/)]),
	[
#include <libHX.h>
#if !defined(_LIBHX_H) || _LIBHX_H < 20070701
#	error You need a newer version of libHX (at least 1.10.0)
#endif
])

m4_sinclude(m4lib/getpwuid.m4)

AC_CFLAGS="-D_LARGEFILE_SOURCE=1 -D_LARGE_FILES -D_FILE_OFFSET_BITS=64 \
	-D_REENTRANT -Wall -Waggregate-return -Wmissing-declarations \
	-Wmissing-prototypes -Wredundant-decls -Wshadow -Wstrict-prototypes \
	-Winline -pipe"
AC_SUBST(AC_CFLAGS)
AC_SUBST(AC_LIBS)
AC_SUBST(ttyreplay_AC_LIBS)
AC_SUBST(rpld_AC_LIBS)
AC_OUTPUT([Makefile doc/GNUmakefile locale/GNUmakefile user/Makefile])
