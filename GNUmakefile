# -*- Makefile -*-

include buildtool/makevars.inc
KERNEL_OS  := $(shell uname -s)
KERNEL_VER := $(shell uname -r | cut -d- -f1 | cut -d. -f1-2)
-include buildtool/Makefile.std.${KERNEL_OS}

CFLAGS   += ${EXT_CFLAGS}
CXXFLAGS += ${EXT_CXXFLAGS}
ASFLAGS  += ${EXT_ASFLAGS}
LDFLAGS  += ${EXT_LDFLAGS}
SOFLAGS  += ${EXT_SOFLAGS}
ARFLAGS  += ${EXT_ARFLAGS}

V_KMOD_SUCCESS := \
  echo "*** Kernel module compiled, installed and loaded. Either edit"; \
  echo "*** your system configuration files so that it is also loaded"; \
  echo "*** upon next bootup. You can leave it as it is if you use"; \
  echo "*** etc-init.d-rpld, since it will try to load it anyway.";

#------------------------------------------------------------------------------
.PHONY: all kmod locale doc clean distclean

all: locale rpld rplctl ttyreplay

-include buildtool/Makefile.kmod.${KERNEL_OS}

locale:
	${Q}${MAKE} -C locale;

ttyreplay: user/replay.o user/pctrl.o user/lib.o
	${VECHO_LD}
	${Q}${LD} ${LDFLAGS} -o $@ $^ -lHX -pthread ${EXT_LDLIBS};
	${Q}${STRIP} -s $@;

rpld: user/rpld.o user/infod.o user/rplctl.o user/rdsh.o user/lib.o
	${VECHO_LD}
	${Q}${LD} ${LDFLAGS} -o $@ $^ -lHX -pthread ${EXT_LDLIBS};
	${Q}${STRIP} -s $@;

rplctl: rpld
	ln -fs $< $@;

%.o: %.c
	${VECHO_CC}
	${Q}${CC} ${CFLAGS} -Wp,-MMD,$(@D)/.$(@F).d -c -o $@ $<;

doc: $(patsubst %.php,%.html,$(wildcard doc/[a-z]*.php)) \
     $(patsubst %.php,%.txt,$(wildcard doc/[a-z]*.php))

%.html: %.php $(wildcard doc/_*.php)
	php -q $< man | perl -pe 's{href="([^/]*?)\.php"}{href="$$1.html"}' >$@;

%.txt: %.html
	w3m -dump $< >$@;

install: ttyreplay rpld rplctl locale
	@echo "Installing to ${ROOT}${PREFIX}/{s,}bin/ and .../etc/";
	install -dm0755 ${ROOT}/etc;
	install -dm0755 ${ROOT}${PREFIX}/bin;
	install -dm0755 ${ROOT}${PREFIX}/sbin;
	install -pm0755 ttyreplay ${ROOT}${PREFIX}/bin/;
	install -pm0755 rpld ${ROOT}${PREFIX}/sbin/;
	ln -fs rpld ${ROOT}${PREFIX}/sbin/rplctl;
	[ ! -e ${ROOT}/etc/rpld.conf ] && \
	    install -pm0644 rpld.conf ${ROOT}/etc/ || :;
ifeq (${KERNEL_OS},Linux)
	install -dm0755 ${ROOT}/etc/init.d;
	perl -pe 's{^DAEMON_BIN=.*}{DAEMON_BIN="'"${PREFIX}"'/sbin/rpld";}' \
	 <etc-init.d-rpld >"${ROOT}/etc/init.d/rpld" && \
	 chmod a+x "${ROOT}/etc/init.d/rpld";
endif
	${MAKE} -C locale install;

install_doc:
	install -dm0755 ${ROOT}${PREFIX}/share/doc/packages/ttyrpld;
	install -pm0644 doc/*.{css,html,png} ${ROOT}${PREFIX}/share/doc/packages/ttyrpld/;

uninstall:
	rm -f ${ROOT}${PREFIX}/bin/ttyreplay ${ROOT}${PREFIX}/sbin/{rpld,rplctl};
	rm -f ${ROOT}/etc/rpld.conf;
	rm -f ${ROOT}/etc/init.d/rpld;
	rm -f ${ROOT}${PREFIX}/share/locale/*/LC_MESSAGES/ttyrpld.mo;

clean:
	rm -f user/*.o rpld rplctl ttyreplay;

distclean: clean
	rm -f doc/*.html doc/*.txt user/.*.d;
	${MAKE} -C locale clean;

-include user/.*.d

#==============================================================================
