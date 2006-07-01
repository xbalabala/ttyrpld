/*=============================================================================
ttyrpld - TTY replay daemon
k_linux-2.6/rpldev.c - Kernel interface for RPLD
  Copyright © Jan Engelhardt <jengelh [at] gmx de>, 2004 - 2006
  -- License restrictions apply (LGPL v2.1)

  This file is part of ttyrpld.
  ttyrpld is free software; you can redistribute it and/or modify it
  under the terms of the GNU Lesser General Public License as published
  by the Free Software Foundation; however ONLY version 2 of the License.

  ttyrpld is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
  Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public
  License along with this program kit; if not, write to:
  Free Software Foundation, Inc., 51 Franklin St, Fifth Floor,
  Boston, MA  02110-1301  USA

  -- For details, see the file named "LICENSE.LGPL2"
=============================================================================*/
#include <asm/byteorder.h>
#include <asm/semaphore.h>
#include <asm/uaccess.h>
#include <linux/compiler.h>
#include <linux/fs.h>
#include <linux/init.h>
#include <linux/kernel.h>
#include <linux/km_rpldev.h>
#include <linux/major.h>
#include <linux/miscdevice.h>
#include <linux/module.h>
#include <linux/moduleparam.h>
#include <linux/poll.h>
#include <linux/string.h>
#include <linux/time.h>
#include <linux/tty.h>
#include <linux/wait.h>
#include <linux/version.h>
#include <linux/vmalloc.h>
#include "../include/rpl_ioctl.h"
#include "../include/rpl_packet.h"

#ifndef __kernel
#    define __kernel
#endif
#ifndef __user
#    define __user
#endif

#define PREFIX "rpldev: "
#define MAXFNLEN 256

#if LINUX_VERSION_CODE < KERNEL_VERSION(2, 6, 0)
#    define KERNEL_2_4 1
#    define IS_PTY_MASTER(tty) \
      (((tty)->driver.major >= UNIX98_PTY_MASTER_MAJOR && \
      (tty)->driver.major < UNIX98_PTY_MASTER_MAJOR + \
      UNIX98_PTY_MAJOR_COUNT) || (tty)->driver.major == PTY_MASTER_MAJOR)
#    define TTY_DEVNR(tty) \
      cpu_to_le32(mkdev_26(MAJOR((tty)->device), MINOR((tty)->device)))
#else
#    define IS_PTY_MASTER(tty) \
      ((tty)->driver->major == UNIX98_PTY_MASTER_MAJOR || \
      (tty)->driver->major == PTY_MASTER_MAJOR)
#    define TTY_DEVNR(tty) \
      cpu_to_le32(mkdev_26((tty)->driver->major, \
      (tty)->driver->minor_start + (tty)->index))
#endif
#ifndef SEEK_SET
#    define SEEK_SET 0
#endif
#ifndef SEEK_CUR
#    define SEEK_CUR 1
#endif
#ifndef SEEK_END
#    define SEEK_END 2
#endif
#define SKIP_PTM(tty)   if(IS_PTY_MASTER(tty)) return 0;

// Stage 2 functions
static int rpldhk_init(struct tty_struct *, struct tty_struct *, struct file *);
static int rpldhk_open(struct tty_struct *, struct tty_struct *, struct file *);
static int rpldhk_read(const char __user *, size_t, struct tty_struct *);
static int rpldhk_write(const char __user *, size_t, struct tty_struct *);
static int rpldhk_ioctl(struct tty_struct *, struct tty_struct *, unsigned int,
    unsigned long);
static int rpldhk_close(struct tty_struct *, struct tty_struct *);
static int rpldhk_deinit(struct tty_struct *, struct tty_struct *);

// Stage 3 functions
static int     rpldev_open(struct inode *, struct file *);
static ssize_t rpldev_read(struct file *, char __user *, size_t, loff_t *);
static loff_t  rpldev_seek(struct file *, loff_t, int);
static int     rpldev_ioctl(struct inode *, struct file *, unsigned int,
                          unsigned long);
static unsigned int rpldev_poll(struct file *, poll_table *);
static int rpldev_close(struct inode *, struct file *);

// Local functions
static inline size_t avail_R(void);
static inline size_t avail_W(void);
static inline void fill_time(struct timeval *);
static inline unsigned int min_uint(unsigned int, unsigned int);
static inline uint32_t mkdev_26(unsigned long, unsigned long);
static inline int circular_get(char __user *, size_t);
static inline void circular_put(const void __kernel *, size_t);
static inline void circular_putU(const void __user *, size_t);
static int circular_put_packet(struct rpldev_packet *, const void *, size_t);

// Variables
static DECLARE_WAIT_QUEUE_HEAD(Pull_queue);
static DECLARE_MUTEX(Buffer_lock);
static DECLARE_MUTEX(Open_lock);
static char *Buffer = NULL, *BufRP = NULL, *BufWP = NULL;
static size_t Bufsize = 32 * 1024;
static unsigned int Minor_nr = MISC_DYNAMIC_MINOR, Open_count = 0,
    Enable_ioctl_proc = 0;

// Kernel module info (kmi) stuff
static struct file_operations kmi_fops = {
    .open    = rpldev_open,
    .read    = rpldev_read,
    .llseek  = rpldev_seek,
    .ioctl   = rpldev_ioctl,
    .poll    = rpldev_poll,
    .release = rpldev_close,
    .owner   = THIS_MODULE,
};
static struct miscdevice kmi_miscinfo = {
    .minor = MISC_DYNAMIC_MINOR,
    .name  = "rpl",
    .fops  = &kmi_fops,
};

MODULE_DESCRIPTION("ttyrpld kernel interface");
MODULE_AUTHOR("Jan Engelhardt <jengelh@gmx.de>");
MODULE_LICENSE("GPL and additional rights");
MODULE_PARM_DESC(Bufsize, "Buffer size (default 32K)");
MODULE_PARM_DESC(Minor_nr, "Minor number to use (default: 255(=DYNAMIC))");
#ifdef KERNEL_2_4
MODULE_PARM(Bufsize, "i");
MODULE_PARM(Minor_nr, "i");
EXPORT_NO_SYMBOLS;
#warning Ahem. Go upgrade to 2.6.x so I can remove support for this ancient stuff.
#else
module_param_named(buffer_size, Bufsize, uint, S_IRUGO);
module_param_named(ioctl_proc, Enable_ioctl_proc, uint, S_IRUGO);
module_param_named(minor, Minor_nr, uint, S_IRUGO);
#endif

//-----------------------------------------------------------------------------
__init static int rpldev_init(void) {
    int ret;
    kmi_miscinfo.minor = Minor_nr;
    if((ret = misc_register(&kmi_miscinfo)) != 0)
        return ret;
    Minor_nr = kmi_miscinfo.minor; // give minor back to sysfs
    printk(KERN_INFO PREFIX "registered at minor %d\n", Minor_nr);
    return 0;
}

__exit static void rpldev_exit(void) {
    misc_deregister(&kmi_miscinfo);
    return;
}

module_init(rpldev_init);
module_exit(rpldev_exit);

//-----------------------------------------------------------------------------
static int rpldhk_init(struct tty_struct *tty, struct tty_struct *ctl,
 struct file *filp)
{
    /* Called from drivers/char/tty_io.c:init_dev() when the refcount of a tty
    raises from zero to one. Usually, an EVT_OPEN follows an EVT_INIT. */
    struct rpldev_packet p;
    char dev[MAXFNLEN], *full_dev;
    int len;

    SKIP_PTM(tty);

    p.dev   = TTY_DEVNR(tty);
    p.event = EVT_INIT;
    p.magic = MAGIC_SIG;
    fill_time(&p.time);

    /* "/dev/tty" is an evil case, because its ownership is not the same as
    that of a better node, e.g. /dev/tty1. Do not pass it to userspace. */
    if(filp->f_dentry->d_inode->i_rdev == MKDEV(TTYAUX_MAJOR, 0) ||
     IS_ERR(full_dev = d_path(filp->f_dentry, filp->f_vfsmnt,
     dev, sizeof(dev)))) {
        p.size = 0;
        return circular_put_packet(&p, NULL, 0);
    }

    p.size = cpu_to_le16(len = strlen(full_dev));
    return circular_put_packet(&p, full_dev, len);
}

static int rpldhk_open(struct tty_struct *tty, struct tty_struct *ctl,
 struct file *filp)
{
    /* Called from drivers/char/tty_io.c:tty_open() whenever an open() on a
    tty succeeds. */
    struct rpldev_packet p;
    char dev[MAXFNLEN], *full_dev;
    int len;

    SKIP_PTM(tty);

    p.dev   = TTY_DEVNR(tty);
    p.event = EVT_OPEN;
    p.magic = MAGIC_SIG;
    fill_time(&p.time);

    if(filp->f_dentry->d_inode->i_rdev == MKDEV(TTYAUX_MAJOR, 0) ||
     IS_ERR(full_dev = d_path(filp->f_dentry, filp->f_vfsmnt,
     dev, sizeof(dev)))) {
        p.size = 0;
        return circular_put_packet(&p, NULL, 0);
    }

    p.size = cpu_to_le16(len = strlen(full_dev));
    return circular_put_packet(&p, full_dev, len);
}

static int rpldhk_read(const char __user *buf, size_t count,
 struct tty_struct *tty)
{
    /* The data flow is a bit weird at first. rpldhk_read() gets the data on
    its way between ttyDriver(master) -> /dev/stdin(slave), meaning this
    function is called when you hit the keyboard. _Even_ if you do not see any
    text onscreen. */
    struct rpldev_packet p;

    SKIP_PTM(tty);
    if(count == 0)
        return 0;

    p.dev   = TTY_DEVNR(tty);
    p.size  = cpu_to_le16(count);
    p.event = EVT_READ;
    p.magic = MAGIC_SIG;
    fill_time(&p.time);
    return circular_put_packet(&p, buf, count);
}

static int rpldhk_write(const char __user *buf, size_t count,
 struct tty_struct *tty)
{
    /* Data flow: /dev/stdout(slave) -> tty driver(master).
    There are two ways an application can use a tty:

    1. noecho mode (the case with interactive shells)
       Shells print chars as they see fit, thus generating EVT_READ for you
       hitting a key, and EVT_WRITE for the shell displaying it.

    2. echo mode (cat waiting for EOF on stdin)
       The _tty driver_ echoes _single_ chars back. This generates EVT_READ for
       your keyboard interaction, plus _one_ EVT_WRITE when an end-of-line is
       received.

    Sounds familiar? Anyway, don't care, we log it all in userspace. You can
    use the "E" key during ttyreplay which shows EVT_READ events which is all
    you need if the EVT_WRITE packets do not suffice. */
    struct rpldev_packet p;

    SKIP_PTM(tty);
    if(count == 0)
        return 0;

    p.dev   = TTY_DEVNR(tty);
    p.size  = cpu_to_le16(count);
    p.event = EVT_WRITE;
    p.magic = MAGIC_SIG;
    fill_time(&p.time);
    return circular_put_packet(&p, buf, count);
}

static int rpldhk_ioctl(struct tty_struct *tty, struct tty_struct *ctl,
 unsigned int cmd, unsigned long arg)
{
    struct rpldev_packet p;
    uint32_t cmd32;

    SKIP_PTM(tty);

    cmd32   = cmd;
    p.dev   = TTY_DEVNR(tty);
    p.size  = cpu_to_le16(sizeof(cmd32));
    p.event = EVT_IOCTL;
    p.magic = MAGIC_SIG;
    fill_time(&p.time);
    return circular_put_packet(&p, &cmd32, sizeof(cmd32));
}

static int rpldhk_close(struct tty_struct *tty, struct tty_struct *other) {
    struct rpldev_packet p;

    SKIP_PTM(tty);

    p.dev   = TTY_DEVNR(tty);
    p.size  = 0;
    p.event = EVT_CLOSE;
    p.magic = MAGIC_SIG;
    fill_time(&p.time);
    return circular_put_packet(&p, NULL, 0);
}

static int rpldhk_deinit(struct tty_struct *tty, struct tty_struct *other) {
    struct rpldev_packet p;

    if(IS_PTY_MASTER(tty))
        tty = other;

    p.dev   = TTY_DEVNR(tty);
    p.size  = 0;
    p.event = EVT_DEINIT;
    p.magic = MAGIC_SIG;
    fill_time(&p.time);
    return circular_put_packet(&p, NULL, 0);
}

//-----------------------------------------------------------------------------
static int rpldev_open(struct inode *inode, struct file *filp) {
    // This one is called when the device node has been opened.
    if(inode != NULL) {
        inode->i_mtime = CURRENT_TIME;
        inode->i_mode &= ~(S_IWUGO | S_IXUGO);
    }

    /* The RPL device should only be opened once, since otherwise, different
    packets could go to different readers. */
    down(&Open_lock);
    if(Open_count) {
        up(&Open_lock);
        return -EBUSY;
    }
    ++Open_count;
    up(&Open_lock);

    if((Buffer = kmalloc(Bufsize, GFP_KERNEL)) == NULL) {
        Buffer = __vmalloc(Bufsize, GFP_KERNEL, PAGE_KERNEL);
        if(Buffer == NULL) {
            --Open_count;
            return -ENOMEM;
        }
    }
    BufRP = BufWP = Buffer;

    /* Update links. I do it here 'cause I do not want memory copying (from the
    tty driver to the buffer) when there is no one to read. */
    rpl_init   = rpldhk_init;
    rpl_open   = rpldhk_open;
    rpl_read   = rpldhk_read;
    rpl_write  = rpldhk_write;
    rpl_close  = rpldhk_close;
    rpl_deinit = rpldhk_deinit;

    if(Enable_ioctl_proc)
        rpl_ioctl = rpldhk_ioctl;

    /* The inode's times are changed as follows:
            Access Time: if data is read from the device
      Inode Change Time: when the device is successfully opened
      Modification Time: whenever the device is opened
    */
    if(inode != NULL)
        inode->i_ctime = CURRENT_TIME;
    return 0;
}

static ssize_t rpldev_read(struct file *filp, char __user *buf, size_t count,
 loff_t *ppos)
{
    int ret = 0;

    // Nothing read, nothing done
    if(count == 0)
        return 0;

    // Must sleep as long as there is no data
    if(down_interruptible(&Buffer_lock))
        return -ERESTARTSYS;
    if(Buffer == NULL)
        goto out;

    while(BufRP == BufWP) {
        up(&Buffer_lock);
        if(filp->f_flags & O_NONBLOCK)
            return -EAGAIN;
        if(wait_event_interruptible(Pull_queue, (BufRP != BufWP)))
            return -ERESTARTSYS;
        if(down_interruptible(&Buffer_lock))
            return -ERESTARTSYS;
        if(Buffer == NULL)
            goto out;
    }

    // Data is available, so give it to the user
    count = min_uint(count, avail_R());
    ret   = circular_get(buf, count);

 out:
    up(&Buffer_lock);
    filp->f_dentry->d_inode->i_atime = CURRENT_TIME;
    return (ret != 0) ? ret : count;
}

static loff_t rpldev_seek(struct file *filp, loff_t offset, int origin) {
    /* Since you can not seek in the circular buffer, the only purpose of
    seek() is to skip some bytes and "manually" advance the read pointer RP
    (BufRP). Thus, the only accepted origin is SEEK_CUR, or SEEK_END
    with offset == 0. */
    int ret = -ESPIPE;

    if(origin == SEEK_END && offset == 0) {
        if(down_interruptible(&Buffer_lock))
            return -ERESTARTSYS;
        if(Buffer == NULL)
            goto out;
        BufRP = BufWP;
        up(&Buffer_lock);
        filp->f_dentry->d_inode->i_atime = CURRENT_TIME;
        return 0;
    }

    if(origin != SEEK_CUR)
        return -ESPIPE;
    if(offset == 0)
        return 0;
    if(down_interruptible(&Buffer_lock))
        return -ERESTARTSYS;
    BufRP = Buffer + (BufRP - Buffer + min_uint(offset, avail_R())) % Bufsize;
    ret = 0;
 out:
    up(&Buffer_lock);
    filp->f_dentry->d_inode->i_atime = CURRENT_TIME;
    return ret;
}

static int rpldev_ioctl(struct inode *inode, struct file *filp,
 unsigned int cmd, unsigned long arg)
{
    int ret = 0;

    if(_IOC_TYPE(cmd) != RPL_IOC_MAGIC)
        return -ENOTTY; /* ENAUGHTY */

    switch(cmd) {
        case RPL_IOC_GETBUFSIZE:
            return Bufsize;
        case RPL_IOC_GETRAVAIL:
            if(down_interruptible(&Buffer_lock))
                return -ERESTARTSYS;
            ret = avail_R();
            up(&Buffer_lock);
            return ret;
        case RPL_IOC_GETWAVAIL:
            if(down_interruptible(&Buffer_lock))
                return -ERESTARTSYS;
            ret = avail_W();
            up(&Buffer_lock);
            return ret;
        case RPL_IOC_IDENTIFY:
            return 0xC0FFEE;
        case RPL_IOC_SEEK:
            return rpldev_seek(filp, arg, SEEK_CUR);
        case RPL_IOC_FLUSH:
            return rpldev_seek(filp, 0, SEEK_END);
    }

    ret = -ENOTTY;
    return ret;
}

static unsigned int rpldev_poll(struct file *filp, poll_table *wait) {
    poll_wait(filp, &Pull_queue, wait);
    return POLLIN | POLLRDNORM;
}

static int rpldev_close(struct inode *inode, struct file *filp) {
    rpl_init   = NULL;
    rpl_open   = NULL;
    rpl_read   = NULL;
    rpl_write  = NULL;
    rpl_ioctl  = NULL;
    rpl_close  = NULL;
    rpl_deinit = NULL;

    down(&Buffer_lock);
    vfree(Buffer);
    Buffer = NULL;
    up(&Buffer_lock);
    --Open_count;
    return 0;
}

//-----------------------------------------------------------------------------
/* Some cases to consider - and proof of correctness
assuming Buffer=(void*)20, Bufsize=8 for simplicity
┌────┬──┬──┬──┬──┬──┬──┬──┬──┬─────────────────────┬─────────────────────────┐
│Case│20│21│22│23│24│25│26│27│ avail_R             │ avail_W()               │
├────┼──┼──┼──┼──┼──┼──┼──┼──┼─────────────────────┼─────────────────────────┤
│ #1 │RW│  │  │  │  │  │  │  │ W-R   = 20-20   = 0 │ R+s-W-1 = 20+8-20-1 = 7 │
│ #2 │  │R │  │  │ W│  │  │  │ W-R   = 24-21   = 3 │ R+s-W-1 = 21+8-24-1 = 4 │
│ #3 │R │  │  │  │  │  │  │ W│ W-R   = 28-20   = 7 │ R+s-W-1 = 20+8-27-1 = 0 │
│ #4 │ W│ R│  │  │  │  │  │  │ W+s-R = 20+8-21 = 7 │ R-W-1   = 21-20-1   = 0 │
│ #5 │ W│  │  │  │  │  │  │ R│ W+s-R = 20+8-27 = 1 │ R-W-1   = 27-20-1   = 6 │
└────┴──┴──┴──┴──┴──┴──┴──┴──┴─────────────────────┴─────────────────────────┘
*/

static inline size_t avail_R(void) {
    // Return the number of available bytes to read
    if(BufWP >= BufRP)
        return BufWP - BufRP;
    return BufWP + Bufsize - BufRP;
}

static inline size_t avail_W(void) {
    // Return the number of available bytes to write
    if(BufWP >= BufRP)
        return BufRP + Bufsize - BufWP - 1;
    return BufRP - BufWP - 1;
}

static inline void fill_time(struct timeval *tv) {
    do_gettimeofday(tv);

    /* The following code all gets optimized away on little-endian.
    On big-endian, it is reduced by 50%. */
    if(sizeof(tv->tv_sec) == sizeof(uint32_t))
        tv->tv_sec = cpu_to_le32(tv->tv_sec);
    else if(sizeof(tv->tv_sec) == sizeof(uint64_t))
        tv->tv_sec = cpu_to_le64(tv->tv_sec);

    if(sizeof(tv->tv_usec) == sizeof(uint32_t))
        tv->tv_usec = cpu_to_le32(tv->tv_usec);
    else if(sizeof(tv->tv_usec) == sizeof(uint64_t))
        tv->tv_usec = cpu_to_le64(tv->tv_usec);

    return;
}

static inline unsigned int min_uint(unsigned int a, unsigned int b) {
    return (a < b) ? a : b;
}

static inline uint32_t mkdev_26(unsigned long maj, unsigned long min) {
    return (maj << 20) | (min & 0xFFFFF);
}

static inline int circular_get(char __user *dest, size_t count) {
    /* circular_get() reads COUNT bytes from the circulary buffer and puts it
    into userspace memory. The caller must make sure that COUNT is at most
    avail_R(). */
    size_t x = Buffer + Bufsize - BufRP; // bytes until "end" of circular buffer
    int ret  = 0;

    /* It is advised that the userspace daemon has DEST memory-locked to
    minimize blocking operation due to swapped-out page faults. */
    if(count < x) {
        if((ret = copy_to_user(dest, BufRP, count)) == 0)
            BufRP += count;
    } else {
        if((ret = copy_to_user(dest, BufRP, x)) == 0 &&
         (ret = copy_to_user(dest + x, Buffer, count - x)) == 0)
            BufRP = Buffer + count - x;
    }

    return ret;
}

static inline void circular_put(const void __kernel *src, size_t count) {
    /* This function is responsible for copying (a specific amount of
    arbitrary data) into the circulary buffer. (Taking the wrap-around into
    account!) The parent function -- circular_put_packet() in most cases --
    must make sure there is enough room. */
    size_t x = Buffer + Bufsize - BufWP;

    if(count < x) {
        memcpy(BufWP, src, count);
        BufWP += count;
    } else {
        memcpy(BufWP, src, x);
        memcpy(Buffer, src + x, count - x);
        BufWP = Buffer + count - x;
    }

    return;
}

static inline void circular_putU(const void __user *src, size_t count) {
    size_t x = Buffer + Bufsize - BufWP;

    if(count < x) {
        if(copy_from_user(BufWP, src, count) == 0)
            BufWP += count;
    } else {
        if(copy_from_user(BufWP, src, x) == 0 &&
         copy_from_user(Buffer, src + x, count - x) == 0)
            BufWP = Buffer + count - x;
    }

    return;
}

static int circular_put_packet(struct rpldev_packet *p, const void *buf,
 size_t count)
{
    /* Copy the contents the tty driver received to our circulary buffer and
    also add a header (rpld_packet) to it so that the userspace daemon can
    recognize the type. */

    /* "sizeof(struct rpldev_packet) + count" might overflow. Therefore, the
    greatest value COUNT can have without overflowing is
    ~0 - sizeof(struct rpldev_packet). */
    if(count > (size_t)(-sizeof(struct rpldev_packet) - 1))
        return -ENOSPC;
    if(down_interruptible(&Buffer_lock))
        return -ERESTARTSYS;
    if(Buffer == NULL) {
        up(&Buffer_lock);
        return 0;
    }
    if(avail_W() < sizeof(struct rpldev_packet) + count) {
        up(&Buffer_lock);
        return -ENOSPC;
    }

    circular_put(p, sizeof(struct rpldev_packet));
    if(count > 0) {
        if(__addr_ok(buf))
            circular_putU(buf, count);
        else
            circular_put(buf, count);
    }

    up(&Buffer_lock);
    wake_up(&Pull_queue);
    return count;
}

//=============================================================================