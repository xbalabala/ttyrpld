#ifndef TTYRPLD_ENDIAN_H
#define TTYRPLD_ENDIAN_H 1

#if defined(__FreeBSD__) || defined(__NetBSD__)
#	include <sys/endian.h>
#	ifndef __BYTE_ORDER
#		define __BYTE_ORDER	_BYTE_ORDER
#		define __LITTLE_ENDIAN _LITTLE_ENDIAN
#		define __BIG_ENDIAN	_BIG_ENDIAN
#	endif
#elif defined(__OpenBSD__)
#	include <machine/endian.h>
#	ifdef BYTE_ORDER
#		define __BYTE_ORDER	BYTE_ORDER
#		define __LITTLE_ENDIAN LITTLE_ENDIAN
#		define __BIG_ENDIAN	BIG_ENDIAN
#	endif
#elif defined(__sun__)
#	define __LITTLE_ENDIAN 1234
#	define __BIG_ENDIAN    4321
#	ifdef __sparc_
#		define __BYTE_ORDER __BIG_ENDIAN
#	else
#		define __BYTE_ORDER __LITTLE_ENDIAN
#	endif
#else
#	include <endian.h>
/* __BYTE_ORDER defined */
#endif

#define swab16(x) ((((x) & 0xFF) << 8) | (((x) & 0xFF00) >> 8))
#define swab32(x) ((((x) & 0xFF) << 24) | (((x) & 0xFF00) << 8) | \
	(((x) & 0xFF0000) >> 8) | (((x) & 0xFF000000) >> 24))
#define swab64(x) (swab32(((x) >> 32) & 0xFFFFFFFF) | \
	((long long)swab32((x) & 0xFFFFFFFF) << 32))

#if __BYTE_ORDER == __BIG_ENDIAN
#	define cpu_to_le16(x) swab16(x)
#	define cpu_to_le32(x) swab32(x)
#	define cpu_to_le64(x) swab64(x)
#	define le16_to_cpu(x) swab16(x)
#	define le32_to_cpu(x) swab32(x)
#	define le64_to_cpu(x) swab64(x)
#	define cpu_to_be16(x) (x)
#	define cpu_to_be32(x) (x)
#	define cpu_to_be64(x) (x)
#	define be16_to_cpu(x) (x)
#	define be32_to_cpu(x) (x)
#	define be64_to_cpu(x) (x)
#else
#	define cpu_to_le16(x) (x)
#	define cpu_to_le32(x) (x)
#	define cpu_to_le64(x) (x)
#	define le16_to_cpu(x) (x)
#	define le32_to_cpu(x) (x)
#	define le64_to_cpu(x) (x)
#	define cpu_to_be16(x) swab16(x)
#	define cpu_to_be32(x) swab32(x)
#	define cpu_to_be64(x) swab64(x)
#	define be16_to_cpu(x) swab16(x)
#	define be32_to_cpu(x) swab32(x)
#	define be64_to_cpu(x) swab64(x)
#endif

#endif /* RPL_ENDIAN_H */
