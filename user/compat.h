#ifndef TTYRPLD_COMPAT_H
#define TTYRPLD_COMPAT_H 1

#include "config.h"

#if defined(HAVE_GETPWUID5)
static inline struct passwd *rpld_getpwnam(const char *user,
    struct passwd *buffer, char *resbuf, size_t buflen)
{
	struct passwd *ret;
	getpwnam_r(user, buffer, resbuf, buflen, &ret);
	return ret;
}

static inline struct passwd *rpld_getpwuid(uid_t uid, struct passwd *resbuf,
    char *buffer, size_t buflen)
{
	struct passwd *ret;
	getpwuid_r(uid, resbuf, buffer, buflen, &ret);
	return ret;
}
#elif defined(HAVE_GETPWUID4)
#	define rpld_getpwnam getpwnam_r
#	define rpld_getpwuid getpwuid_r
#else
#	define rpld_getpwnam(user, rbuf, buffer, buflen) getpwnam(user)
#	define rpld_getpwuid(uid, rbuf, buffer, buflen)  getpwuid(uid)
#endif

#endif /* TTYRPLD_COMPAT_H */
