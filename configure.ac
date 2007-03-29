
AC_INIT(ttyrpld, 2.19)
AC_CONFIG_HEADERS(config.h)
AM_INIT_AUTOMAKE
AC_PROG_CC

AC_HAVE_HEADERS(alloca.h)
AC_CHECK_LIB([rt], [nanosleep], [ttyreplay_AC_LDLIBS="$ttyreplay_AC_LDLIBS -lrt"])
AC_CHECK_LIB([socket], [accept], [rpld_AC_LDLIBS="$rpld_AC_LDLIBS -lsocket -lnsl"])
AC_CHECK_HEADER([libHX.h], [], AC_MSG_ERROR([Need at least libHX 1.10]), [
#include <libHX.h>
#if !defined(_LIBHX_H) || _LIBHX_H < 20070320
#	error ----------------------------------------
#	error You need a newer version of libHX
#	error (at least 1.10.0)
#	error URL: jengelh.hopto.org/p/libHX/
#	error ----------------------------------------
#endif
])

m4_sinclude(m4lib/getpwuid.m4)

AC_CFLAGS="-D_LARGEFILE_SOURCE=1 -D_LARGE_FILES -D_FILE_OFFSET_BITS=64 \
	-D_REENTRANT -Wall -Waggregate-return -Wmissing-declarations \
	-Wmissing-prototypes -Wredundant-decls -Wshadow -Wstrict-prototypes \
	-Winline -pipe"
AC_SUBST(AC_CFLAGS)
AC_SUBST(ttyreplay_AC_LDLIBS)
AC_SUBST(rpld_AC_LDLIBS)
AC_OUTPUT(Makefile doc/GNUmakefile user/Makefile)
