<?php include_once("_header.php"); ?>
  <tr>
    <td align="right" valign="top"
      class="section"><b>Requirements&nbsp;&gt;</b></td>
    <td valign="top">

      <table border="1" class="sBorder">
        <tr>
          <td><i>Component</i></td>
          <td><i>Minimum</i></td>
          <td><i>Recommended</i>, or better</td>
        </tr>
        <tr>
          <td>GNU make</td>
          <td>3.79</td>
          <td>3.80</td>
        </tr>
        <tr>
          <td>BSD make<br />
            (only applies to *BSD targets)</td>
          <td colspan="2"><i>as shipped with your distribution</i></td>
        </tr>
        <tr>
          <td><a href="http://jengelh.hopto.org/coding/libHX.php">libHX</a></td>
          <td>1.8.0</li>
          <td>1.8.1</li>
        </tr>
        <tr>
          <td>GNU C Compiler</td>
          <td>3.3</li>
          <td>4.x</li>
        </tr>
        <tr>
          <td colspan="3"><i>Auxiliary tools (i.e. unpacking, documentation,
            etc.&nbsp;-- not strictly needed on the compiling machine)</i></td>
        </tr>
        <tr>
          <td>perl</td>
          <td>5.6.0</td>
          <td>&gt;= 5.6.0</td>
        </tr>
        <tr>
          <td>php</td>
          <td>4.x</td>
          <td>&gt;= 4.x</td>
        </tr>
        <tr>
          <td>w3m</td>
          <td>0.5.0</td>
          <td>0.5.1</td>
        </tr>
        <tr>
          <td>bzip2, patch, tar</td>
          <td colspan="2"><i>whatever works</i></td>
        </tr>
        <tr>
          <td colspan="3"><i>Please check your kernel documentation on what
            more tools are needed for a kernel compilation.</i></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top"
      class="section"><b>Unpacking&nbsp;&gt;</b></td>
    <td valign="top">

      <p class="block">In the following code snippets, <tt><b>#</b></tt>
      denotes a root/superuser shell, while <tt><b>$</b></tt> <i>can</i> be a
      user, or a root shell. Listed are only the commands you have to execute,
      their output is not shown in the boxes.</p>

      <p class="block">If you made it until here, I assume you already
      unpackaged the source tarball. If not, use:</p>

      <div class="indent">
        <p class="code"><tt>
        $ <b>tar -xvjf ttyrpld-<i>VERSION</i>.tbz2;</b>
        </tt></p>
      </div>

      <p class="block">Of course, replace <i>version</i> with the correct
      string. Non-GNU users might need some additional steps or different ways
      to unpack <i>tar-bzip2</i> archives.</p>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>Patching the Kernel
      sources (Linux)&nbsp;&gt;</b></td>
    <td valign="top">

      <p class="block">This is probably the step most of you will hate, because
      it involves a recompile of the Kernel, or at least <tt>bzImage</tt>. 
      There is currently no other way. Once you have it prepared with the
      <i>rpldev</i> hooks, you will never need to do so again, when you either
      get a new Kernel tree or if the <i>kpatch</i>es change (which is rare).
      That way, you can upgrade <i>ttyrpld</i> (userspace) or the <i>rpldev</i>
      kernel module without a reboot.</p>

      <p class="block">You can find different Kernel patches in the
      <tt>kpatch</tt> folder, each targeting a different series and/or version. 
      You can patch your Kernel source tree by issuing these commands:</p>

      <div class="indent">
        <p class="code"><tt>
        $ <b>cd /usr/src/linux-2.6.15/;</b><br />
        $ <b>patch -p1 -i /usr/src/ttyrpld/kpatch/linux-2.6.15.diff;</b><br />
        $ <b>make menuconfig;</b>
        </tt></p>
      </div>

      <p class="block">Run `<tt>make menuconfig</tt>` or anything else that
      suits your needs to choose "<tt>TTY logging via rpldev</tt>" in the
      "<tt>Security options</tt>" menu.</p>

      <p class="block"><i>Note: For Linux Kernels 2.4.x, the "TTY logging via
      rpldev" option is available in the "Character devices" menu.</p>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>Patching the Kernel
      sources (FreeBSD)&nbsp;&gt;</b></td>
    <td valign="top">

      <p class="block">The procedure is similar to the one needed for the Linux
      Kernel. The kpatch for FreeBSD is a little bigger than the one for Linux,
      since it has to add a close-per-fd method and coherent hooks for these. 
      As such, not all of the exotic tty devices might be supported under
      FreeBSD.</p>

      <div class="indent">
        <p class="code"><tt>
        $ <b>cd /usr/src/sys/;</b><br />
        $ <b>patch -p1 -i /usr/src/ttyrpld/kpatch/freebsd-6.1.diff;</b>
        </tt></p>
      </div>

      <p class="block">... and rebuild the kernel image. No option needs to be
      enabled in <tt>conf</tt>. The procedure on how to build the Kernel under
      FreeBSD might vary from person to person, but I generally prefer to
      install all the <tt>src</tt> packages, and use:</p>

      <div class="indent">
        <p class="code"><tt>
        $ <b>cd /usr/src/;</b><br />
        $ <b>make kernel</b><br />
        </tt></p>
      </div>

      <p class="block">For `<tt>make kernel</tt>` to work, various source
      packages may need to be installed.</p>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>Patching the Kernel
      sources (OpenBSD and NetBSD)&nbsp;&gt;</b></td>
    <td valign="top">

      <p class="block">The process is not much different:</p>

      <div class="indent">
        <p class="code"><tt>
        $ <b>cd /usr/src/sys/;</b><br />
        $ <b>patch -p1 -i /usr/src/ttyrpld/kpatch/openbsd-3.9.diff;</b>
        </tt></p>
      </div>

      <p class="block">Rebuild as described in <tt>afterboot(8)</tt>:</p>

      <div class="indent">
        <p class="code"><tt>
        $ <b>cd /usr/src/sys/arch/i386/conf/;</b><br />
        $ <b>config GENERIC</b><br />
        $ <b>cd ../compile/GENERIC</b><br />
        $ <b>make depend</b><br />
        $ <b>make</b><br />
        ---<br />
        $ <b>cp bsd /bsd &amp;&amp; reboot</b>
        </tt></p>
      </div>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top" class="section"><b>Compiling the Kernel
      module&nbsp;&gt;</b></td>

    <td valign="top">

      <p class="block">After having booted the new Kernel, run `<tt>make
      kmod</tt>` in the root of the source directory. Note that you will need
      <i>GNU make</i>, version 3.80 or later. It will try to compile, load and
      install the module.</p>

      <p class="block">You can tune the location of the Kernel sources and
      module installation path by using <tt>KERNEL_DIR=xxx</tt> and/or
      <tt>MODULES_DIR=xxx</tt> options to <i>make</i>, respectively.</p>

      <p class="block">BSD users have to use <tt>gmake</tt>;
      <tt>KERNEL_DIR</tt> and <tt>MODULES_DIR</tt> is not applicable for BSD,
      but you may <tt>cd</tt> into <tt>freebsd-6.1</tt> by hand and run (bsd)
      <tt>make</tt> with any options you need. The kmod target <i>will not</i>
      install the module on FreeBSD.</p>

      <p class="block">OpenBSD users can skip this step, as they have a
      monolithic kernel.</p>

      <p class="block">To load the module at boot time, edit your distribution
      specific startup scripts. A hint for SUSE users: it is in
      <tt>/etc/sysconfig/kernel</tt> in <tt>MODULES_LOADED_ON_BOOT</tt>. If you
      do not know where, ask someone who knows or simply put a `<tt>modprobe
      rpldev</tt>` into <tt>/etc/init.d/boot.local</tt>. However, the supplied
      <tt>etc-init.d-rpld</tt> script (to be put as <tt>/etc/init.d/rpld</tt>)
      (for Linux/LSB) will be so kind to automatically load it.</p>

      <p class="block">There is currently a start script for <i>rpld</i> for
      Linux only.</p>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>Compiling the user-space
      applications&nbsp;&gt;</b></td>
    <td valign="top">

      <p class="block">To compile the user-space tools, run `<tt>make</tt>`
      (BSD: `<tt>gmake</tt>`) without any arguments. This will build
      <tt>rpld</tt> and <tt>ttyreplay</tt> (the daemon and the log analyzer).
      If you want to build HTML and TXT documentation from the PHP files, you
      need to use `<tt>gmake doc</tt>`.</p>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top"
    class="section"><b>Installing&nbsp;&gt;</b></td>

    <td valign="top">

      <p class="block">A `<tt>make install</tt>` target is provided and will
      install <i>ttyrpld</i> into various places in <tt>${ROOT}${PREFIX}</tt>.
      This probably needs some explanation: <tt>ROOT</tt> is a make variable
      which is prepended to all copy operations, etc., so you can use for
      example `<tt>make install ROOT=/var/netboot</tt>` to have everything
      installed under this alternative root directory.</p>

      <p class="block">The <tt>PREFIX</tt> make variable works almost the same,
      but is also used when constructing files. That is, if you run `<tt>make
      install PREFIX=/altdisk</tt>`, the init script will be adjusted to
      contain <tt>/altdisk/usr/sbin/rpld</tt>. By default, <tt>PREFIX</tt>
      contains <tt>/usr/local</tt>, so that binaries are installed to
      <tt>/usr/local/sbin</tt>. If you passed <tt>PREFIX=/usr</tt> instead,
      binaries would go into <tt>/usr/sbin/</tt>.</p>

      <p class="block">Locations are:<br />
      <tt>rpld</tt>, <tt>rplctl</tt> =&gt;
      <tt>${ROOT}${PREFIX}/sbin/rpld</tt><br />
      <tt>ttyreplay</tt> =&gt; <tt>${ROOT}${PREFIX}/bin</tt><br />
      Locale data =&gt; <tt>${ROOT}${PREFIX}/share/locale</tt></p>

      <p class="block">The configuration file will always get installed to
      <tt>${ROOT}/etc/rpld.conf</tt>! If it already exists, it is not
      overwritten.</p>

      <p class="block">The init script (only Linux) will always get installed
      to <tt>${ROOT}/etc/init.d/</tt>! However, it is not automatically
      activated to be run on bootup. Check your distribution's manual on how to
      do this. SUSE users get away with `<tt>insserv rpld</tt>`.</p>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top" class="section"><b>Device
    node&nbsp;&gt;</b></td>

    <td valign="top">

      <p class="block">Details are on the <a
      href="man_rpldev.4.php"><tt>rpldev(4)</tt></a> manpage.</p>

    </td>
  </tr>
<?php include_once("_footer.php"); ?>
