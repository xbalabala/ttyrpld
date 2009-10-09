/*
 *	Copyright © Jan Engelhardt <jengelh [at] medozas de>, 2004 - 2009
 *
 *	This file is part of ttyrpld. ttyrpld is free software; you can
 *	redistribute it and/or modify it under the terms of the GNU
 *	Lesser General Public License as published by the Free Software
 *	Foundation; either version 2 or 3 of the License.
 */
#ifndef TTYRPLD_LIB_H
#define TTYRPLD_LIB_H 1

#include <sys/types.h>
#include <locale.h>
#include <libintl.h>
#include "rpl_endian.h"
#include "rpl_stdint.h"

#ifndef O_BINARY
#	define O_BINARY 0
#endif
#define _(s) gettext(s)

#define MAXFNLEN 256

struct pctrl_info;

extern void load_locale(const char *);
extern ssize_t read_through(int, int, size_t);
extern ssize_t read_wait(int, void *, size_t, const struct pctrl_info *);
extern off_t G_skip(int, off_t, int);

static inline unsigned int min_uint(unsigned int a, unsigned int b)
{
	return (a < b) ? a : b;
}

#endif /* TTYRPLD_LIB_H */
