#ifndef TTYRPLD_PACKET_H
#define TTYRPLD_PACKET_H 1

#define __PACKED __attribute__((packed))

#if defined(__linux__) && defined(__KERNEL__)
#	include <linux/time.h>
#	include <linux/types.h>
#elif (defined(__FreeBSD__) || defined(__OpenBSD__) || defined(__sun)) && \
    defined(_KERNEL)
#	include <sys/time.h>
#	include <sys/types.h>
#else /* userspace */
#	include <sys/time.h>
#	include "rpl_stdint.h"
#endif

enum {
	RPLEVT_MASK       = 0x727033E0,
	RPLEVT_NONE       = 0x72703340, /* "rp3@" */
	RPLEVT_OPEN       = 0x72703341, /* "rp3A" */
	RPLEVT_READ       = 0x72703342, /* "rp3B" */
	RPLEVT_WRITE      = 0x72703343, /* "rp3C" */
	RPLEVT_LCLOSE     = 0x72703344, /* "rp3D" */
	RPLEVT_ID_PROG    = 0x72703345, /* "rp3E" */
	RPLEVT_ID_DEVPATH = 0x72703346, /* "rp3F" */
	RPLEVT_ID_TIME    = 0x72703347, /* "rp3G" */
	RPLEVT_ID_USER    = 0x72703348, /* "rp3H" */
	RPLEVT_max,
};

struct rpltime {
	uint64_t tv_sec;
	uint32_t tv_usec;
};

union rpldev_evmagic {
	uint32_t n;
	char m[4];
};

struct rpldev_packet {
	union rpldev_evmagic evmagic;
	uint32_t size;
	struct rpltime time;
	uint32_t dev;
} __PACKED;

struct rpldsk_packet {
	union rpldev_evmagic evmagic;
	uint32_t size;
	struct rpltime time;
} __PACKED;

#endif /* TTYRPLD_PACKET_H */
