# -*- Makefile -*-

.PHONY: all clean reload

all: rpldhk rpldev

rpldhk: rpldhk.o
	gld -r -o $@ $^;

rpldev:	rpldev.o
	gld -r -o $@ $^;

%.o: %.c
	gcc -D_KERNEL -I. -O2 -Wall -fno-pic -nodefaultlibs -c -o $@ $<;

clean:
	rm -f *.o rpldhk rpldev;

reload: rpldev
	rem_drv rpldev; cp rpldev /usr/kernel/drv/; add_drv rpldev;

#<EOF>
