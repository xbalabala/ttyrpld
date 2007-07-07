/*
 *	ttyrpld/user/pctrl.h
 *	Copyright © Jan Engelhardt <jengelh [at] gmx de>, 2004 - 2007
 *
 *	This file is part of ttyrpld. ttyrpld is free software; you can
 *	redistribute it and/or modify it under the terms of the GNU
 *	Lesser General Public License as published by the Free Software
 *	Foundation; either version 2 or 3 of the License.
 */
#ifndef TTYRPLD_PCTRL_H
#define TTYRPLD_PCTRL_H 1

enum {
	PCTRL_NONE = 0,
	PCTRL_PREV,
	PCTRL_NEXT,
	PCTRL_EXIT,
	PCTRL_SKPACK,
	PCTRL_SKTIME,
};

struct pctrl_info {
	double factor;
	int sktype;
	long skval;
	unsigned char paused, brk, echo;
};

extern int pctrl_init(void);
extern void pctrl_exit(void);
extern void pctrl_activate(struct pctrl_info *);
extern void pctrl_deactivate(int);

#endif /* TTYRPLD_PCTRL_H */
