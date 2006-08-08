# -*- Makefile -*-

all: rpldhk rpldev

load:
	rem_drv rpldev; cp rpldev /usr/kernel/drv/; add_drv rpldev;

rpldhk: rpldhk.o
	gld -r -o $@ $^

rpldev:	rpldev.o
	gld -r -o $@ $^

%.o: %.c
	gcc -D_KERNEL -I. -O2 -Wall -fno-pic -nodefaultlibs -c -o $@ $<
