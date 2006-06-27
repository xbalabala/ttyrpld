# -*- Makefile -*-

MODULES_DIR := /lib/modules/`uname -r`
KERNEL_DIR  := ${MODULES_DIR}/build

.PHONY: all modules install clean

all: kmod

kmod:
	make -C ${KERNEL_DIR} SUBDIRS=$$PWD modules;

install: kmod
	mkdir -p ${MODULES_DIR}/misc;
	cp $< ${MODULES_DIR}/misc/;

clean:
	rm -f .rpldev.o.flags rpldev.o;

obj-m += rpldev.o
-include ${TOPDIR}/Rules.make

#<<eof>>
