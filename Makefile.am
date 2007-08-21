# -*- Makefile -*-

AUTOMAKE_OPTIONS = foreign subdir-objects

if GOT_TOOLS_FOR_DOC
doc_subdir       = doc
endif

if GOT_TOOLS_FOR_LOCALE
locale_subdir    = locale
endif

SUBDIRS          = etc user $(doc_subdir) $(locale_subdir)
