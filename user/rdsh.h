/*
 *	ttyrpld/user/rdsh.h
 *	Copyright © Jan Engelhardt <jengelh [at] gmx de>, 2004 - 2007
 *
 *	This file is part of ttyrpld. ttyrpld is free software; you can
 *	redistribute it and/or modify it under the terms of the GNU
 *	Lesser General Public License as published by the Free Software
 *	Foundation; either version 2 or 3 of the License.
 */
#ifndef TTYRPLD_RDSH_H
#define TTYRPLD_RDSH_H 1

#include <sys/types.h>
#include <pthread.h>
#include "rpl_stdint.h"
#include <libHX.h>

/* Definitions */
#define RECEIVE_FULL(fd, ptr, sz) \
	(recv((fd), (ptr), (sz), MSG_WAITALL) == (sz))

/* Code annotations */
#define reinterpret_cast(type, expr) ((type)(expr))
#define static_cast(type, expr)      ((type)(expr))

struct GOptmap_t {
	int syslog, verbose;
	long user_id;
	const char *infod_port, *ofmt;
};

struct Statmap_t {
	unsigned long open, read, write, lclose, badpack;
	unsigned long long in, out;
};

struct tty {
	uint32_t dev;
	uid_t uid;
	int fd, status;
	unsigned long in, out;
	hmc_t *log;
	char *sdev, *full_dev;
};

enum {
	IFP_NONE = 0x3F,
	IFP_DEFAULT,
	IFP_ACTIVATE,
	IFP_DEACTIVATE,
	IFP_DEACTIVSES,
	IFP_REMOVE,
	IFP_GETINFO,
	IFP_GETINFO_T,
	IFP_ZERO,
};

/*
 *	INFOD.C
 */
extern void infod_init(void);
extern void *infod_main(void *);

/*
 *	RPLCTL.C
 */
extern int rplctl_main(int, const char **);

/*
 *	RDSH.C
 */
extern pthread_mutex_t Ttys_lock;
extern const char *Device_dirs[];
extern struct HXbtree *Ttys;
extern struct Statmap_t Stats;
extern struct GOptmap_t GOpt;

extern struct tty *get_tty(uint32_t, int);
extern void log_close(struct tty *);
extern void notify(int, const char *, ...);
extern ssize_t send_wait(int, const void *, size_t, int);

#endif /* TTYRPLD_RDSH_H */
