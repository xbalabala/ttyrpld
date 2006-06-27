# -*- Makefile -*-

# PREFIX will be overriden by RPM SPECFILE
PREFIX      := /usr/local
LOCALE_PATH := ${ROOT}${PREFIX}/share/locale
ALL_MO      := $(patsubst %.po,%.mo,$(wildcard */LC_MESSAGES/*.po))

.PHONY: all install uninstall clean

all: ${ALL_MO}

%.mo: %.po
	@echo -en "$@: ";
	@msgfmt --statistics -o $@ $<;

install: all
	@echo "Installing locale files to ${LOCALE_PATH}";
	for i in *; do \
            [ ! -d "$$i" ] && continue; \
            install -dm0755 "${LOCALE_PATH}/$$i/LC_MESSAGES"; \
            install -m0644 "$$i/LC_MESSAGES/ttyrpld.mo" "${LOCALE_PATH}/$$i/LC_MESSAGES/"; \
        done;

uninstall:
	rm -f ${LOCALE_PATH}/*/LC_MESSAGES/ttyrpld.mo;

clean:
	rm -f ${ALL_MO};

#==============================================================================
