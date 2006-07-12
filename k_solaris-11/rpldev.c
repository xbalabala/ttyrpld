#include <sys/devops.h>
#include <sys/modctl.h>

static struct cb_ops rpldev_cbops = {
    .cb_open     = nodev, // rpldev_open
    .cb_close    = nodev, // rpldev_close
    .cb_strategy = nodev,
    .cb_print    = nodev,
    .cb_dump     = nodev,
    .cb_read     = nodev, // rpldev_read
    .cb_write    = nodev, // rpldev_write
    .cb_ioctl    = nodev, // uio_ioctl
    .cb_devmap   = nodev,
    .cb_mmap     = nodev,
    .cb_segmap   = nodev,
    .cb_chpoll   = nodev,
    .cb_prop_op  = nodev,
    .cb_rev      = CB_REV,
    .cb_aread    = nodev,
    .cb_awrite   = nodev,
};

static struct dev_ops rpldev_devops = {
    .devo_rev    = DEVO_REV,
//    .devo_attach = ??_attach,
//    .devo_detach = ??_detach,
    .devo_cb_ops = &rpldev_cbops,
};    

static struct modldrv rpldev_modldrv = {
    .drv_modops   = &mod_driverops,
    .drv_linkinfo = "rpldev tty grabber",
    .drv_dev_ops  = &rpldev_devops,
};

static struct modlinkage rpldev_modlinkage = {
    .ml_rev     = MODREV_1,
    .ml_linkage = {&rpldev_modldrv},
};

//-----------------------------------------------------------------------------
int _init(void) {
    int ret;

    if((ret = mod_install(&rpldev_modlinkage)) != 0)
        return ret;

    return 0;
}

int _fini(void) {
    return mod_remove(&rpldev_modlinkage);
}

int _info(struct modinfo *i) {
    return mod_info(&rpldev_modlinkage, i);
}

//=============================================================================
