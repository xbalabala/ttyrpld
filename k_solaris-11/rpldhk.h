#ifndef _RPLDHK_H
#define _RPLDHK_H 1

#include <sys/types.h>

extern int (*rpl_init)(struct queue *);
extern int (*rpl_open)(struct queue *);
extern int (*rpl_read)(const char *, size_t, struct queue *);
extern int (*rpl_write)(const char *, size_t, struct queue *);
extern int (*rpl_ioctl)(struct queue *);
extern int (*rpl_close)(struct queue *);
extern int (*rpl_deinit)(struct queue *);

#endif /* _RPLDHK_H */
