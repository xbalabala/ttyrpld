
AC_INIT(ttyrpld, 2.19)
AC_CONFIG_HEADERS(config.h)
AM_INIT_AUTOMAKE
AC_PROG_CC

AC_HAVE_HEADERS(alloca.h)

AM_CFLAGS="-D_LARGEFILE_SOURCE=1 -D_LARGE_FILES -D_FILE_OFFSET_BITS=64 \
	-D_REENTRANT -Wall -Waggregate-return -Wmissing-declarations \
	-Wmissing-prototypes -Wredundant-decls -Wshadow -Wstrict-prototypes \
	-Winline -pipe"
AC_SUBST(AM_CFLAGS)
AC_OUTPUT(Makefile doc/GNUmakefile user/Makefile)
