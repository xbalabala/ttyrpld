# -*- Makefile -*-

.PHONY: all clean reload

all: rpldhk rpldev

rpldhk: rpldhk.o
	gld -r -o $@ $^;

# Requires Solaris LD
rpldev:	rpldev.o
	/usr/ccs/bin/ld -N"strmod/rpldhk" -dy -r -o $@ $^;

%.o: %.c
	gcc -D_KERNEL -I. -O2 -Wall -fno-pic -nodefaultlibs -c -o $@ $<;

clean:
	rm -f *.o rpldhk rpldev;

reload: rpldev
	-rem_drv rpldev;
	cp rpldev rpldev.conf /usr/kernel/drv/;
	add_drv rpldev;
