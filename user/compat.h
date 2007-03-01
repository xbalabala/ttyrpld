#ifndef TTYRPLD_COMPAT_H
#define TTYRPLD_COMPAT_H 1

#if defined(__OpenBSD__)
#	/* lacks it */
#	define rpld_getpwnam(user, rbuf, buffer, buflen) getpwnam(user)
#	define rpld_getpwuid(uid, rbuf, buffer, buflen)  getpwuid(uid)
#elif defined(__linux__) || defined(__FreeBSD__)
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
#endif

#endif /* TTYRPLD_COMPAT_H */
