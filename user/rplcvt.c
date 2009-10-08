/*
 *	ttyrpld/user/rplcvt.c
 *	Copyright © Jan Engelhardt <jengelh [at] medozas de>, 2007 - 2008
 *
 *	This file is part of ttyrpld. ttyrpld is free software; you can
 *	redistribute it and/or modify it under the terms of the GNU
 *	Lesser General Public License as published by the Free Software
 *	Foundation; either version 2 or 3 of the License.
 */
#include <sys/stat.h>
#include <sys/types.h>
#include <errno.h>
#include <fcntl.h>
#include <stdbool.h>
#include <stdio.h>
#include <stdint.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <libHX/option.h>
#include "../include/rpl_packet.h"
#include "lib.h"

/* Definitions */
enum {
	F_NONE,
	F_RPLDSK1_32,
	F_RPLDSK1_64,
	F_RPLDSK2,
};

struct rpldsk1_packet32 {
	uint16_t size;
	uint8_t event, magic;
	struct {
		uint32_t tv_sec;
		uint32_t tv_usec;
	} time;
} __PACKED;

struct rpldsk1_packet64 {
	uint16_t size;
	uint8_t event, magic;
	struct {
		uint64_t tv_sec;
		uint64_t tv_usec;
	} time;
};

struct rpldsk2_packet {
	uint16_t size;
	uint8_t event, magic;
	struct rpltime time;
} __PACKED;

/* Functions */
static bool rplcvt_get_options(int *, const char ***);
static void rplcvt1_32(int);
static void rplcvt1_64(int);

/* Variables */
static char *rplcvt_format;

//-----------------------------------------------------------------------------
static void rplcvt2(int fd)
{
	struct rpldsk2_packet old;
	struct rpldsk_packet new;
	ssize_t ret;

	while ((ret = read(fd, &old, sizeof(old))) == sizeof(old)) {
		new.size         = old.size;
		new.event        = old.event;
		new.magic        = old.magic;
		new.time.tv_sec  = old.time.tv_sec;
		new.time.tv_usec = old.time.tv_usec;
		write(STDOUT_FILENO, &new, sizeof(new));
		read_through(fd, STDOUT_FILENO, new.size);
	}
}

int main(int argc, const char **argv)
{
	int fd;

	if (!rplcvt_get_options(&argc, &argv))
		return EXIT_FAILURE;

	if (argc > 2) {
		fprintf(stderr, "%s: At most one filename is allowed.\n",
		        *argv);
		return EXIT_FAILURE;
	} else if (argc == 1) {
		fd = STDIN_FILENO;
	} else {
		if ((fd = open(argv[1], O_RDONLY)) < 0) {
			fprintf(stderr, "%s: Could not open %s: %s\n",
			        *argv, argv[1], strerror(errno));
			return EXIT_FAILURE;
		}
	}

	if (strcmp(rplcvt_format, "2") == 0) {
		rplcvt2(fd);
	} else if (strcmp(rplcvt_format, "1@32") == 0) {
		rplcvt1_32(fd);
	} else if (strcmp(rplcvt_format, "1@64") == 0) {
		rplcvt1_64(fd);
	} else {
		fprintf(stderr, "%s: Unknown format specified with -F\n", *argv);
		return EXIT_FAILURE;
	}

		return EXIT_FAILURE;

	close(fd);
	return EXIT_SUCCESS;
}

static bool rplcvt_get_options(int *argc, const char ***argv)
{
	struct HXoption options_table[] = {
		{.sh = 'F', .type = HXTYPE_STRING, .ptr = &rplcvt_format,
		 .help = "Input format (\"1@32\", \"1@64\", \"2\")"},
		HXOPT_AUTOHELP,
		HXOPT_TABLEEND,
	};
	if (HX_getopt(options_table, argc, argv, HXOPT_USAGEONERR) <= 0)
		return false;
	if (rplcvt_format == NULL) {
		fprintf(stderr, "The -F option is mandatory\n");
		return false;
	}
	return true;
}

static void rplcvt1_32(int fd)
{
	struct rpldsk1_packet32 old;
	struct rpldsk_packet new;
	ssize_t ret;

	while ((ret = read(fd, &old, sizeof(old))) == sizeof(old)) {
		new.size         = old.size;
		new.event        = old.event;
		new.magic        = old.magic;
		new.time.tv_sec  = old.time.tv_sec;
		new.time.tv_usec = old.time.tv_usec;
		write(STDOUT_FILENO, &new, sizeof(new));
		read_through(fd, STDOUT_FILENO, new.size);
	}
}

static void rplcvt1_64(int fd)
{
	struct rpldsk1_packet64 old;
	struct rpldsk_packet new;
	ssize_t ret;

	while ((ret = read(fd, &old, sizeof(old))) == sizeof(old)) {
		new.size         = old.size;
		new.event        = old.event;
		new.magic        = old.magic;
		new.time.tv_sec  = old.time.tv_sec;
		new.time.tv_usec = old.time.tv_usec;
		write(STDOUT_FILENO, &new, sizeof(new));
		read_through(fd, STDOUT_FILENO, new.size);
	}
}

