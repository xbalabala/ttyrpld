/*=============================================================================
ttyrpld - TTY replay daemon
k_solaris-11/rpldhk.c - Kernel interface for RPLD
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
#include <sys/ddi.h>
#include <sys/modctl.h>
#include <sys/sunddi.h>
#include <sys/stream.h>

// Functions
static int rpldhk_rdopen(queue_t *, dev_t *, int, int, cred_t *);
static int rpldhk_rdput(queue_t *, mblk_t *);
static int rpldhk_rdclose(queue_t *, int, cred_t *);

static int rpldhk_wropen(queue_t *, dev_t *, int, int, cred_t *);
static int rpldhk_wrput(queue_t *, mblk_t *);
static int rpldhk_wrclose(queue_t *, int, cred_t *);

// Variables

// Module info
static struct module_info rpldhk_rdinfo = {
    .mi_idnum  = 1337,
    .mi_idname = "rpldhk",
    .mi_minpsz = 0,
    .mi_maxpsz = INFPSZ,
    .mi_hiwat  = 300,
    .mi_lowat  = 200,
};

static struct module_info rpldhk_wrinfo = {
    .mi_idnum  = 1338,
    .mi_idname = "rpldhk",
    .mi_minpsz = 0,
    .mi_maxpsz = INFPSZ,
    .mi_hiwat  = 2048,
    .mi_lowat  = 128,
};

static struct qinit rpldhk_rdinit = {
    .qi_putp   = rpldhk_rdput,
    .qi_qopen  = rpldhk_rdopen,
    .qi_qclose = rpldhk_rdclose,
    .qi_minfo  = &rpldhk_rdinfo,
};

static struct qinit rpldhk_wrinit = {
    .qi_putp   = rpldhk_wrput,
    .qi_qopen  = rpldhk_wropen,
    .qi_qclose = rpldhk_wrclose,
    .qi_minfo  = &rpldhk_wrinfo,
};

static struct streamtab rpldhk_strops = {
    .st_rdinit = &rpldhk_rdinit,
    .st_wrinit = &rpldhk_wrinit,
};

static struct fmodsw rpldhk_devops = {
    .f_name = "rpldhk",
    .f_str  = &rpldhk_strops,
    .f_flag = D_MP, /* D_NEW */
};

static struct modlstrmod rpldhk_modldrv = {
    .strmod_modops   = &mod_strmodops,
    .strmod_linkinfo = "ttyrpld/2.18 rpldhk",
    .strmod_fmodsw   = &rpldhk_devops,
};

static struct modlinkage rpldhk_modlinkage = {
    .ml_rev     = MODREV_1,
    .ml_linkage = {&rpldhk_modldrv, NULL},
};

//-----------------------------------------------------------------------------
int _init(void) {
    return mod_install(&rpldhk_modlinkage);
}

int _info(struct modinfo *mip) {
    return mod_info(&rpldhk_modlinkage, mip);
}

int _fini(void) {
    return mod_remove(&rpldhk_modlinkage);
}

//-----------------------------------------------------------------------------
static int rpldhk_rdopen(queue_t *q, dev_t *dev, int oflag, int sflag,
 cred_t *cred)
{
    cmn_err(CE_NOTE, "%s dev=%ld\n", __FUNCTION__, (long)*dev);
    return 0;
}

static int rpldhk_rdput(queue_t *q, mblk_t *mp) {
    cmn_err(CE_NOTE, "%s\n", __FUNCTION__);
    return 0;
}

static int rpldhk_rdclose(queue_t *q, int flag, cred_t *cred) {
    cmn_err(CE_NOTE, "%s\n", __FUNCTION__);
    return 0;
}

static int rpldhk_wropen(queue_t *q, dev_t *dev, int oflag, int sflag,
 cred_t *cred)
{
    cmn_err(CE_NOTE, "%s dev=%ld\n", __FUNCTION__, (long)*dev);
    return 0;
}

static int rpldhk_wrput(queue_t *q, mblk_t *mp) {
    cmn_err(CE_NOTE, "%s\n", __FUNCTION__);
    return 0;
}

static int rpldhk_wrclose(queue_t *q, int flag, cred_t *cred) {
    cmn_err(CE_NOTE, "%s\n", __FUNCTION__);
    return 0;
}

//=============================================================================
