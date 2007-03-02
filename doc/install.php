<?php include_once("_header.php"); ?>

<h1>Requirements</h1>

<table border="1">
  <tr>
    <td class="t1"><i>Component</i></td>
    <td class="t1"><i>Minimum</i></td>
    <td class="t1"><i>Recommended</i>, or better</td>
  </tr>
  <tr>
    <td class="t2">GNU make</td>
    <td class="t2">3.80</td>
    <td class="t2">3.81</td>
  </tr>
  <tr>
    <td class="t1">BSD make<br />
      (only applies to *BSD targets)</td>
    <td class="t1" colspan="2"><i>as shipped with your distribution</i></td>
  </tr>
  <tr>
    <td class="t2"><a
      href="http://jengelh.hopto.org/coding/libHX.php">libHX</a></td>
    <td class="t2">1.8.0</li>
    <td class="t2">1.9.2</li>
  </tr>
  <tr>
    <td class="t1">GNU C Compiler</td>
    <td class="t1">3.3</li>
    <td class="t1">4.x</li>
  </tr>
  <tr>
    <td class="t2" colspan="3"><i>Auxiliary tools (i.e. unpacking,
      documentation, etc.&nbsp;-- not strictly needed on the compiling
      machine)</i></td>
  </tr>
  <tr>
    <td class="t1">perl</td>
    <td class="t1">5.6.0</td>
    <td class="t1">-</td>
  </tr>
  <tr>
    <td class="t2">php</td>
    <td class="t2">5.x</td>
    <td class="t2">-</td>
  </tr>
  <tr>
    <td class="t1">w3m</td>
    <td class="t1">0.5.0</td>
    <td class="t1">-</td>
  </tr>
  <tr>
    <td class="t2">bzip2, patch, tar</td>
    <td class="t2" colspan="2"><i>any</i></td>
  </tr>
  <tr>
    <td class="t1" colspan="3"><i>Please check your kernel documentation on
      what more tools are needed for a kernel compilation.</i></td>
  </tr>
</table>

<h1>Unpacking</h1>

<p class="block">In the following code snippets, <code><b>#</b></code> denotes
a root/superuser shell, while <code><b>$</b></code> <i>can</i> be a user, or a
root shell. Listed are only the commands you have to execute, their output is
not shown in the boxes.</p>

<p class="block">If you made it until here, I assume you already unpackaged the
source tarball. If not, use:</p>

<p class="code"><code>
<b>$</b> tar -xvjf ttyrpld-<i>VERSION</i>.tbz2;
</code></p>

<p class="block">Of course, replace <i>version</i> with the correct string.
Non-GNU users might need some additional steps or different ways to unpack
<i>tar-bzip2</i> archives.</p>

<h1>Patching the kernel sources (Linux)</h1>

<p class="block">This is probably the step most of you will hate, because it
involves a recompile of the kernel, or at least <i>bzImage</i>.  There is
currently no other way. Once you have it prepared with the <i>rpldev</i> hooks,
you will never need to do so again, when you either get a new kernel tree or if
the <i>kpatch</i>es change (which is rare). That way, you can upgrade
<i>ttyrpld</i> (userspace) or the <i>rpldev</i> kernel module without a
reboot.</p>

<p class="block">You can find different kernel patches in the
<code>kpatch</code> folder, each targeting a different series and/or version. 
You can patch your kernel source tree by issuing these commands:</p>

<p class="code"><code>
<b>$</b> cd /usr/src/linux-2.6.15/;<br />
<b>$</b> patch -p1 -i /usr/src/ttyrpld/kpatch/linux-2.6.15.diff;<br />
<b>$</b> make menuconfig;
</code></p>

<p class="block">Run <code>make menuconfig</code> or anything else that suits
your needs to choose "<code>TTY logging via rpldev</code>" in the
"<code>Security options</code>" menu.</p>

<p class="block">Note: For Linux kernels 2.4.x, the "<code>TTY logging via
rpldev</code>" option is available in the "Character devices" menu.</p>

<h1>Patching the kernel sources (FreeBSD)</h1>

<p class="block">The procedure is similar to the one needed for the Linux
kernel. The kpatch for FreeBSD is a little bigger than the one for Linux, since
it has to add a close-per-fd method and coherent hooks for these.  As such, not
all of the exotic tty devices might be supported under FreeBSD.</p>

<p class="code"><code>
<b>$</b> cd /usr/src/sys/;<br />
<b>$</b> patch -p1 -i /usr/src/ttyrpld/kpatch/freebsd-6.1.diff;
</code></p>

<p class="block">... and rebuild the kernel image. No option needs to be
enabled in <code>conf</code>. The procedure on how to build the kernel under
FreeBSD might vary from person to person, but I generally prefer to install all
the <code>src</code> packages, and use:</p>

<p class="code"><code>
<b>$</b> cd /usr/src/;<br />
<b>$</b> make kernel<br />
</code></p>

<p class="block">For <code>make kernel</code> to work, various source packages
may need to be installed.</p>

<h1>Patching the kernel sources (OpenBSD and NetBSD)</h1>

<p class="block">The process is not much different:</p>

<p class="code"><code>
<b>$</b> cd /usr/src/sys/;<br />
<b>$</b> patch -p1 -i /usr/src/ttyrpld/kpatch/openbsd-3.9.diff;
</code></p>

<p class="block">Rebuild as described in <code>afterboot(8)</code>:</p>

<p class="code"><code>
<b>$</b> cd /usr/src/sys/arch/i386/conf/;<br />
<b>$</b> config GENERIC<br />
<b>$</b> cd ../compile/GENERIC<br />
<b>$</b> make depend<br />
<b>$</b> make<br />
---<br />
<b>$</b> cp bsd /bsd &amp;&amp; reboot
</code></p>

<h1>Compiling the kernel module</h1>

<p class="block">After having booted the new kernel, run <code>make kmod</code>
in the root of the source directory. Note that you will need <i>GNU make</i>,
version 3.80 or later. It will try to compile, load and install the module.</p>

<p class="block">You can tune the location of the kernel sources and module
installation path by using <code>KERNEL_DIR=xxx</code> and/or
<code>MODULES_DIR=xxx</code> options to <i>make</i>, respectively.</p>

<p class="block">BSD users have to use <code>gmake</code>;
<code>KERNEL_DIR</code> and <code>MODULES_DIR</code> is not applicable for BSD,
but you may <code>cd</code> into <code>freebsd-6.1</code> by hand and run (bsd)
<code>make</code> with any options you need. The kmod target <i>will not</i>
install the module on FreeBSD.</p>

<p class="block">OpenBSD users can skip this step, as they have a monolithic
kernel.</p>

<p class="block">To load the module at boot time, edit your distribution
specific startup scripts. A hint for SUSE users: it is in
<code>/etc/sysconfig/kernel</code> in <code>MODULES_LOADED_ON_BOOT</code>. If
you do not know where, ask someone who knows or simply put a <code>modprobe
rpldev</code> into <code>/etc/init.d/boot.local</code>. However, the supplied
<code>etc-init.d-rpld</code> script (to be put as
<code>/etc/init.d/rpld</code>) (for Linux/LSB) will be so kind to automatically
load it.</p>

<p class="block">There is currently a start script for <i>rpld</i> for Linux
only.</p>

<h1>Compiling the user-space applications</h1>

<p class="block">To compile the user-space tools, run <code>make</code> (BSD:
<code>gmake</code>) without any arguments. This will build <code>rpld</code>
and <code>ttyreplay</code> (the daemon and the log analyzer). If you want to
build HTML and TXT documentation from the PHP files, you need to use
<code>gmake doc</code>.</p>

<h1>Installing</h1>

<p class="block">A <code>make install</code> target is provided and will
install <i>ttyrpld</i> into various places in <code>${ROOT}${PREFIX}</code>.
This probably needs some explanation: <code>ROOT</code> is a make variable
which is prepended to all copy operations, etc., so you can use for example
<code>make install ROOT=/var/netboot</code> to have everything installed under
this alternative root directory.</p>

<p class="block">The <code>PREFIX</code> make variable works almost the same,
but is also used when constructing files. That is, if you run <code>make
install PREFIX=/altdisk</code>, the init script will be adjusted to contain
<code>/altdisk/usr/sbin/rpld</code>. By default, <code>PREFIX</code> contains
<code>/usr/local</code>, so that binaries are installed to
<code>/usr/local/sbin</code>. If you passed <code>PREFIX=/usr</code> instead,
binaries would go into <code>/usr/sbin/</code>.</p>

<p class="block">Locations are:<br />
<code>rpld</code>, <code>rplctl</code> =&gt;
<code>${ROOT}${PREFIX}/sbin/rpld</code><br />
<code>ttyreplay</code> =&gt; <code>${ROOT}${PREFIX}/bin</code><br />
Locale data =&gt; <code>${ROOT}${PREFIX}/share/locale</code></p>

<p class="block">The configuration file will always get installed to
<code>${ROOT}/etc/rpld.conf</code>! If it already exists, it is not
overwritten.</p>

<p class="block">The init script (only Linux) will always get installed to
<code>${ROOT}/etc/init.d/</code>! However, it is not automatically activated to
be run on bootup. Check your distribution's manual on how to do this. SUSE
users get away with <code>insserv rpld</code>.</p>

<h1>Device node</h1>

<p class="block">Details are on the <a
href="man_rpldev.4.php"><code>rpldev(4)</code></a> manpage.</p>

<?php include_once("_footer.php"); ?>
