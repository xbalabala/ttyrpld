# -*- Makefile -*-

MODULES_DIR := /lib/modules/$(shell uname -r)
KERNEL_DIR  := ${MODULES_DIR}/build

obj-m += rpldev.o

.PHONY: all modules modules_install install clean

all: modules

rpldev.ko: modules

modules:
	make -C "${KERNEL_DIR}" M="$$PWD" "$@";

modules_install: rpldev.ko
	make -C "${KERNEL_DIR}" M="$$PWD" "$@";

clean:
	make -C "${KERNEL_DIR}" M="$$PWD" "$@";
	rm -f Module.symvers;
