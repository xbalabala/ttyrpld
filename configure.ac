
AC_INIT([ttyrpld], [2.52])
AC_CONFIG_HEADERS([config.h])
AC_CONFIG_MACRO_DIR([m4])
AC_PROG_INSTALL
AM_INIT_AUTOMAKE([-Wall foreign subdir-objects])
AC_PROG_CC
AM_PROG_CC_C_O

AC_CHECK_LIB([rt], [nanosleep], [ttyreplay_LIBS="$ttyreplay_LIBS -lrt"])
AC_CHECK_LIB([socket], [accept], [rpld_LIBS="$rpld_LIBS -lsocket -lnsl"])
AC_CHECK_LIB([intl], [gettext], [regular_LIBS="$regular_LIBS -lintl"])
PKG_CHECK_MODULES([libHX], [libHX >= 2.0])

AC_PATH_PROG([MSGFMT], [gmsgfmt])
if [[ -z "$MSGFMT" ]]; then
	AC_PATH_PROG([MSGFMT], [msgfmt])
fi;

AM_CONDITIONAL([GOT_TOOLS_FOR_LOCALE],
	[$am_make -v | grep -q GNU && test -n "$MSGFMT"])

CHECK_HAVE_GETPWUID

regular_CFLAGS="-D_FILE_OFFSET_BITS=64 \
	-D_REENTRANT -Wall -Waggregate-return -Wmissing-declarations \
	-Wmissing-prototypes -Wredundant-decls -Wshadow -Wstrict-prototypes \
	-Wformat=2 -pipe"
AC_SUBST([regular_CFLAGS])
AC_SUBST([regular_LIBS])
AC_SUBST([ttyreplay_LIBS])
AC_SUBST([rpld_LIBS])
AC_CONFIG_FILES([Makefile dist/debian/rpld
	dist/suse/Makefile dist/suse/rpld
	etc/Makefile user/Makefile
	doc/Makefile locale/GNUmakefile])
AC_OUTPUT
