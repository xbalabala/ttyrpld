# -*- Makefile -*-

AUTOMAKE_OPTIONS = foreign subdir-objects

if GOT_TOOLS_FOR_DOC
doc_subdir       = doc
endif

if GOT_TOOLS_FOR_LOCALE
locale_subdir    = locale
endif

SUBDIRS          = etc user $(doc_subdir) $(locale_subdir)

packer  = lzip
packext = .tar.lz

.PHONY: tarball
tarball:
	rm -Rf /tmp/${PACKAGE_TARNAME}-${PACKAGE_VERSION};
	pushd ${top_srcdir} && git archive --prefix=${PACKAGE_TARNAME}-${PACKAGE_VERSION}/ HEAD | tar -C /tmp -x && popd;
	pushd /tmp/${PACKAGE_TARNAME}-${PACKAGE_VERSION} && ./autogen.sh && popd;
	tar --use=${packer} -C /tmp -cf ${PACKAGE_TARNAME}-${PACKAGE_VERSION}${packext} --owner=root --group=root ${PACKAGE_TARNAME}-${PACKAGE_VERSION}/;
	rm -Rf /tmp/${PACKAGE_TARNAME}-${PACKAGE_VERSION};
