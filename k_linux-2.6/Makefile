# -*- Makefile -*-

MODULES_DIR := /lib/modules/$(shell uname -r)
KERNEL_DIR  := ${MODULES_DIR}/build

.PHONY: all modules modules_install install clean

all: modules

rpldev.ko: modules

modules:
	make -C ${KERNEL_DIR} M=$$PWD $@;

install: modules_install

modules_install: rpldev.ko
	make -C ${KERNEL_DIR} M=$$PWD $@;

clean:
	make -C ${KERNEL_DIR} M=$$PWD $@;

obj-m += rpldev.o
EXTRA_CFLAGS += -funit-at-a-time  

#<<eof>>
