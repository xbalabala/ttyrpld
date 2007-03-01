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
