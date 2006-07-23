# -*- Makefile -*-

rpldev:	rpldev.o
	gld -r -o $@ $^

rpldev.o: rpldev.c
	gcc -D_KERNEL -I. -O2 -Wall -fno-pic -nodefaultlibs -c -o $@ $<
