/*=============================================================================
ttyrpld - TTY replay daemon
k_solaris-11/rpldev.c - Kernel interface for RPLD
  Copyright © Jan Engelhardt <jengelh [at] gmx de>, 2006
  -- License restrictions apply (LGPL v2.1)

  This program is free software; you can redistribute it and/or modify it
  under the terms of the GNU Lesser General Public License as published
  by the Free Software Foundation; however ONLY version 2 of the License.

  This program is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
  Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public
  License along with this program kit; if not, write to:
  Free Software Foundation, Inc., 51 Franklin St, Fifth Floor,
  Boston, MA  02110-1301  USA

  -- For details, see the file named "LICENSE.LGPL2"
=============================================================================*/
#include <sys/types.h>
#include <sys/ccompile.h>
#include <sys/cmn_err.h>
#include <sys/conf.h>
#include <sys/cred.h>
#include <sys/devops.h>
#include <sys/ddi.h>
#include <sys/errno.h>
#include <sys/file.h>
#include <sys/ksynch.h>
#include <sys/modctl.h>
#include <sys/sunddi.h>
#include <sys/stat.h>
#include <sys/uio.h>
#include <sys/km_rpldev.h>
#include "../include/rpl_ioctl.h"
#include "../include/rpl_packet.h"

// Module stuff
static int rpldev_attach(dev_info_t *, ddi_attach_cmd_t);
static int rpldev_detach(dev_info_t *, ddi_detach_cmd_t);
static int rpldev_getinfo(dev_info_t *, ddi_info_cmd_t, void *, void **);

// Stage 2 functions
/*
static int rpldhc_init(void);
static int rpldhc_open(void);
static int rpldhc_read(void);
static int rpldhc_write(void);
static int rpldhc_ioctl(void);
static int rpldhc_close(void);
static int rpldhc_deinit(void);
*/

// Stage 3 functions
static int rpldev_open(dev_t *, int, int, cred_t *);
static int rpldev_close(dev_t, int, int, cred_t *);
static int rpldev_read(dev_t, struct uio *, cred_t *);
static int rpldev_chpoll(dev_t, short, int, short *, struct pollhead **);

// Local functions
static inline size_t avail_R(void);
static inline size_t avail_W(void);
static inline unsigned int min_uint(unsigned int, unsigned int);
static inline int circular_get(struct uio *, size_t);
static inline void circular_put(const void *, size_t);
static int circular_put_packet(struct rpldev_packet *, const void *, size_t);

// Variables
static struct pollhead   Buffer_queue;
static kcondvar_t        Buffer_wait;
static kmutex_t          Buffer_lock, Open_lock;
static ddi_umem_cookie_t Buffer_cookie;
static char *Buffer = NULL, *BufRP = NULL, *BufWP = NULL;
static size_t Bufsize = 32 * 1024;
static unsigned int Open_count = 0;

// Module stuff
static struct cb_ops rpldev_cbops = {
    .cb_open     = rpldev_open,
    .cb_close    = rpldev_close,
    .cb_strategy = nodev,
    .cb_print    = nodev,
    .cb_dump     = nodev,
    .cb_read     = rpldev_read,
    .cb_write    = nodev, // rpldev_write
    .cb_ioctl    = nodev, // rpldev_ioctl
    .cb_devmap   = nodev,
    .cb_mmap     = nodev,
    .cb_segmap   = nodev,
    .cb_chpoll   = rpldev_chpoll,
    .cb_prop_op  = ddi_prop_op,
    .cb_flag     = D_NEW | D_MP | D_64BIT,
    .cb_rev      = CB_REV,
    .cb_aread    = nodev,
    .cb_awrite   = nodev,
};

static struct dev_ops rpldev_devops = {
    .devo_rev      = DEVO_REV,
    .devo_getinfo  = rpldev_getinfo,
    .devo_identify = nulldev,
    .devo_probe    = nulldev,
    .devo_attach   = rpldev_attach,
    .devo_detach   = rpldev_detach,
    .devo_reset    = nodev,
    .devo_cb_ops   = &rpldev_cbops,
    .devo_power    = nulldev,
};

static struct modldrv rpldev_modldrv = {
    .drv_modops   = &mod_driverops,
    .drv_linkinfo = "ttyrpld/2.18 rpldev",
    .drv_dev_ops  = &rpldev_devops,
};

static struct modlinkage rpldev_modlinkage = {
    .ml_rev     = MODREV_1,
    .ml_linkage = {&rpldev_modldrv, NULL},
};

static dev_info_t *rpldev_dip = NULL;

//-----------------------------------------------------------------------------
int _init(void) {
    mutex_init(&Buffer_lock, NULL, MUTEX_DRIVER, NULL);
    mutex_init(&Open_lock, NULL, MUTEX_DRIVER, NULL);
    cv_init(&Buffer_wait, NULL, CV_DRIVER, NULL);
    cmn_err(CE_NOTE, "rpldev: _init\n");
    return mod_install(&rpldev_modlinkage);
}

int _fini(void) {
    int ret;
    cmn_err(CE_NOTE, "rpldev: _fini\n");
    if((ret = mod_remove(&rpldev_modlinkage)) == 0) {
        mutex_destroy(&Buffer_lock);
        mutex_destroy(&Open_lock);
        cv_destroy(&Buffer_wait);
    }
    return ret;
}

int _info(struct modinfo *i) {
    cmn_err(CE_NOTE, "rpldev: _info\n");
    return mod_info(&rpldev_modlinkage, i);
}

static int rpldev_attach(dev_info_t *dip, ddi_attach_cmd_t cmd) {
    cmn_err(CE_NOTE, "rpldev: rpldev_attach\n");
    switch(cmd) {
        case DDI_ATTACH:
            rpldev_dip = dip;
            if(ddi_create_minor_node(dip, "0", S_IFCHR, ddi_get_instance(dip), DDI_PSEUDO, 0) != DDI_SUCCESS) {
                cmn_err(CE_NOTE, "%s%d: attach: Could not add character node.", "dummy", 0);
                return DDI_FAILURE;
            }
            return DDI_SUCCESS;
        case DDI_RESUME:
            return DDI_SUCCESS;
        default:
            return DDI_FAILURE;
    }
}

static int rpldev_detach(dev_info_t *dip, ddi_detach_cmd_t cmd) {
    cmn_err(CE_NOTE, "rpldev: rpldev_detach\n");
    switch(cmd) {
        case DDI_DETACH:
            rpldev_dip = NULL;
            ddi_remove_minor_node(dip, NULL);
            return DDI_SUCCESS;
        case DDI_SUSPEND:
            return DDI_SUCCESS;
        default:
            return DDI_FAILURE;
    }
}

static int rpldev_getinfo(dev_info_t *dip, ddi_info_cmd_t cmd, void *arg,
 void **resultp)
{
    cmn_err(CE_NOTE, "rpldev: rpldev_getinfo\n");
    switch(cmd) {
        case DDI_INFO_DEVT2DEVINFO:
            *resultp = rpldev_dip;
            return DDI_SUCCESS;
        case DDI_INFO_DEVT2INSTANCE:
            *resultp = NULL;
            return DDI_SUCCESS;
    }
    return DDI_FAILURE;
}

//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
static int rpldev_open(dev_t *devp, int flag, int otyp, cred_t *credp) {
    mutex_enter(&Open_lock);
    if(Open_count) {
        mutex_exit(&Open_lock);
        return EBUSY;
    }
    ++Open_count;
    mutex_exit(&Open_lock);

    if((Buffer = ddi_umem_alloc(Bufsize, 0, &Buffer_cookie)) == NULL) {
        --Open_count;
        return ENOMEM;
    }

    BufRP = BufWP = Buffer;
/*
    rpl_init   = rpldhc_init;
    rpl_open   = rpldhc_open;
    rpl_read   = rpldhc_read;
    rpl_write  = rpldhc_write;
    //rpl_ioctl  = rpldhc_ioctl;
    rpl_close  = rpldhc_close;
    rpl_deinit = rpldhc_deinit;
*/
    strcpy(Buffer, "Just some random bytes to test the functionality");
    BufWP += strlen(Buffer);
    cmn_err(CE_NOTE, "rpldev: opened\n");
    return 0;
}

static int rpldev_read(dev_t dev, struct uio *uio, cred_t *credp) {
    size_t count;
    int ret = 0;

    mutex_enter(&Buffer_lock);
    if(Buffer == NULL)
        goto out;

    cmn_err(CE_NOTE, "rpldev: read\n");
    while(BufRP == BufWP) {
        mutex_exit(&Buffer_lock);
        if(uio->uio_fmode & (FNDELAY | FNONBLOCK))
            return EAGAIN;
        cmn_err(CE_NOTE, "rpldev: waiting in read\n");
        mutex_enter(&Buffer_lock);
        ret = cv_wait_sig(&Buffer_wait, &Buffer_lock);
        mutex_exit(&Buffer_lock);
        if(ret == 0)
            return EINTR;
        cmn_err(CE_NOTE, "rpldev: acquiring in read\n");
        ret = 0;
        mutex_enter(&Buffer_lock);
        if(Buffer == NULL)
            goto out;
    }

    cmn_err(CE_NOTE, "rpldev_read: jo, resid=%d, avail=%d\n",
     (int)uio->uio_resid, (int)avail_R());
    count = min_uint(uio->uio_resid, avail_R());
    cmn_err(CE_NOTE, "rpldev_read: jo, count=%d\n", (int)count);
    ret   = circular_get(uio, count);
    cmn_err(CE_NOTE, "rpldev_read: jo, ret=%d\n", (int)ret);
 out:
    mutex_exit(&Buffer_lock);
    return ret;
}

static int rpldev_ioctl(dev_t dev, int cmd, intptr_t arg, int mode,
 cred_t *credp, int *rvalp)
{
    size_t val;
    int ret = 0;

    if(_IOC_TYPE(cmd) != RPL_IOC_MAGIC)
        return ENOTTY;

    switch(cmd) {
        case RPL_IOC_GETBUFSIZE:
            return (ddi_copyout((void *)arg, &Bufsize,
                   sizeof(Bufsize), 0) != 0) ? EFAULT : 0;
        case RPL_IOC_GETRAVAIL:
            mutex_enter(&Buffer_lock);
            if(Buffer == NULL)
                goto out;
            val = avail_R();
            ret = ddi_copyout((void *)arg, &val, sizeof(val), 0);
            mutex_exit(&Buffer_lock);
            return (ret != 0) ? EFAULT : 0;
        case RPL_IOC_GETWAVAIL:
            mutex_enter(&Buffer_lock);
            if(Buffer == NULL)
                goto out;
            val = avail_W();
            ret = ddi_copyout((void *)arg, &val, sizeof(val), 0);
            mutex_exit(&Buffer_lock);
            return (ret != 0) ? EFAULT : 0;
        case RPL_IOC_IDENTIFY:
            val = 0xC0FFEE;
            return (ddi_copyout((void *)arg, &val,
                   sizeof(val), 0) != 0) ? EFAULT : 0;
        case RPL_IOC_FLUSH:
            mutex_enter(&Buffer_lock);
            BufRP = BufWP;
            mutex_exit(&Buffer_lock);
            return 0;
    }

    ret = ENOTTY;
 out:
    return ret;
}

static int rpldev_chpoll(dev_t dev, short requested_events, int any_yet,
 short *available_events, struct pollhead **pollhd)
{
    cmn_err(CE_NOTE, "rpldev: chpoll rq %d BufRP %p BufWP %p\n",
     requested_events, BufRP, BufWP);
    if((requested_events & (POLLIN | POLLRDNORM)) && BufRP != BufWP) {
        *available_events = POLLIN | POLLRDNORM;
        return 0;
    }

    *available_events = 0;
    if(!any_yet)
        *pollhd = &Buffer_queue;
    return 0;
}

static int rpldev_close(dev_t dev, int flag, int otyp, cred_t *credp) {
/*
    rpl_init   = NULL;
    rpl_open   = NULL;
    rpl_read   = NULL;
    rpl_write  = NULL;
    rpl_ioctl  = NULL;
    rpl_close  = NULL;
    rpl_deinit = NULL;
*/
    mutex_enter(&Buffer_lock);
    ddi_umem_free(Buffer_cookie);
    Buffer = NULL;
    mutex_exit(&Buffer_lock);
    --Open_count;
    cmn_err(CE_NOTE, "rpldev: close\n");
    return 0;
}

//-----------------------------------------------------------------------------
static inline size_t avail_R(void) {
    if(BufWP >= BufRP)
        return BufWP - BufRP;
    return BufWP + Bufsize - BufRP;
}

static inline size_t avail_W(void) {
    if(BufWP >= BufRP)
        return BufRP + Bufsize - BufWP - 1;
    return BufRP - BufWP - 1;
}

static inline unsigned int min_uint(unsigned int a, unsigned int b) {
    return (a < b) ? a : b;
}

static inline int circular_get(struct uio *uio, size_t count) {
    size_t x = Buffer + Bufsize - BufRP;
    int ret;

    if(count < x) {
        if((ret = uiomove(BufRP, count, UIO_READ, uio)) != 0)
            return ret;
        BufRP += count;
    } else {
        if((ret = uiomove(BufRP, x, UIO_READ, uio)) != 0 ||
         (ret = uiomove(Buffer, count - x, UIO_READ, uio)) != 0)
            return ret;
        BufRP = Buffer + count - x;
    }

    uio->uio_loffset = 0;
    return ret;
}

static inline void circular_put(const void *src, size_t count) {
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

static int circular_put_packet(struct rpldev_packet *p, const void *buf,
 size_t count)
{
    if(count > (size_t)(-sizeof(struct rpldev_packet) - 1))
        return ENOSPC;
    mutex_enter(&Buffer_lock);
    if(Buffer == NULL) {
        mutex_exit(&Buffer_lock);
        return 0;
    }
    if(avail_W() < sizeof(struct rpldev_packet) + count) {
        mutex_exit(&Buffer_lock);
        return ENOSPC;
    }

    circular_put(p, sizeof(struct rpldev_packet));
    if(count > 0)
        circular_put(buf, count);
    mutex_exit(&Buffer_lock);
    pollwakeup(&Buffer_queue, POLLIN | POLLRDNORM);
    cv_broadcast(&Buffer_wait);
    return count;
}

//=============================================================================
