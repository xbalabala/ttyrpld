# -*- Makefile -*-

ACLOCAL_AMFLAGS  = -I m4
AUTOMAKE_OPTIONS = foreign subdir-objects

if GOT_TOOLS_FOR_DOC
doc_subdir       = doc
endif

if GOT_TOOLS_FOR_LOCALE
locale_subdir    = locale
endif

SUBDIRS          = dist/suse etc user $(doc_subdir) $(locale_subdir)

tmpdir := $(shell mktemp -dtu)
packer  = bzip2
packext = .tar.bz2

.PHONY: tarball
tarball:
# do not use mkdir_p here.
	mkdir ${tmpdir}
	pushd ${top_srcdir} && git archive --prefix=${PACKAGE_NAME}-${PACKAGE_VERSION}/ HEAD | tar -C ${tmpdir} -x && popd;
	pushd ${tmpdir}/${PACKAGE_NAME}-${PACKAGE_VERSION} && ./autogen.sh && popd;
	tar --use=${packer} -C ${tmpdir} -cf ${PACKAGE_NAME}-${PACKAGE_VERSION}${packext} --owner=root --group=root ${PACKAGE_NAME}-${PACKAGE_VERSION}/;
	rm -Rf ${tmpdir};
