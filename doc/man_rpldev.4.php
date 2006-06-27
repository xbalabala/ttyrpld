<?php include_once("_header.php"); ?>
  <tr>
    <td align="right" valign="top" class="section"><b>Name&nbsp;&gt;</b></td>
    <td valign="top">rpldev - tty logger kernel module</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top"
    class="section"><b>Description&nbsp;&gt;</b></td>

    <td valign="top">

      <p class="block"><tt>/dev/rpl</tt> is a character device file which
      returns the data captured from ttys through the <i>rpldev</i> kernel
      module. It has no fixed device number, since it asks for a dynamic
      major/minor number. Usually, it will get <tt>char(10,63)</tt> or
      <tt>char(10,62)</tt> on Linux&nbsp;-- the Kernel module will emit a
      notice where it actually landed. The kernel hotplug framework and
      <i>udev</i> will then create a device node in <tt>/dev</tt>.</p>

      <p class="block">On FreeBSD, devfs takes care of all this, so when the
      module is loaded, a working <tt>/dev/rpl</tt> is also present. For
      OpenBSD and NetBSD, it always loads at <tt>char(228,0)</tt>. Use
      <tt>mknod /dev/rpl b 228 0</tt> to create the node. It should be made
      mode <tt>0400</tt> and owned by the low-privileged user that is set in
      <tt>rpld.conf</tt>.</p>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top" class="section"><b>Module
    parameters (Linux)&nbsp;&gt;</b></td>

    <td valign="top">

      <p class="block">The <i>rpldev</i> module has the following options. Some
      of them may be changed dynamically at run-time via
      <tt>/sys/module/rpldev/</tt>.</p>

      <div class="indent"><table border="1" class="sBorder">
        <tr>
          <td>Bufsize</td>
          <td>Size of the ring buffer in bytes. Default is 32K.</td>
        </tr>
        <tr>
          <td>Minor_nr</td>
          <td>Force a minor number rather than automatically deciding for one.
            Passing 255 here will use auto-selection, and is the default.</td>
        </tr>
      </table></div>

      <p class="block">These options can be provided to <i>modprobe</i> like
      this:</p>

      <div class="indent">
        <p class="code"><tt>
        # <b>modprobe rpldev Minor_nr=37</b>
        </tt></p>
      </div>

      <p class="block">If you cannot influence the modprobe call, or do not
      should (do not modify <tt>/etc/init.d/rpld</tt> if you can avoid it), you
      can put the options into <tt>/etc/modprobe.conf.local</tt>:</p>

      <div class="indent">
        <p class="code"><tt>options rpldev Minor_nr=37</tt></p>
      </div>

      <p class="block">I have not found any way for passing or modifying
      options under BSD, so unless you are willing to recompile the kernel, you
      are stuck with a fixed 32K buffer.</p>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top" class="section"><b>Data
    structures&nbsp;&gt;</b></td>

    <td valign="top">

      <p class="block">Everytime the kernel functions for reading/writing
      from/to a terminal are executed, the call chain is extened into the
      rpldev hooks, i.e. an external function in the <i>rpldev</i> kernel
      module is called, which transfers the tty buffer into so-called packets,
      which are then returned as <tt>/dev/rpl</tt> is read.</p>

      <p class="block">A packet consists of a device number, magic byte (always
      <tt>0xEE</tt>), size of the data, timestamp, and of course, the data
      itself. All fields are little-endian and packed, i.e. there are no
      alignment gaps. It can be represented in this C struct:</p>

      <div class="indent">
        <p class="code"><tt>
        <b>struct</b> rpldev_packet {<br />
        &nbsp; &nbsp; <b>uint32_t</b> dev;<br />
        &nbsp; &nbsp; <b>uint16_t</b> size;<br />
        &nbsp; &nbsp; <b>uint8_t</b> event, magic;<br />
        &nbsp; &nbsp; <b>struct</b> timeval time;<br />
        } <i>__attribute__((packed))</i>;</tt></p>
      </div>

      <p class="block">The data itself is actually not included in the struct,
      since it is of dynamic length. For further reference, the struct alone
      will be called <i>packet header</i>, whereas packet header plus
      <i>data</i> (or <i>payload</i>) equals <i>packet</i>. There are no
      byte-aligning gaps between the members, i.e. it is a so-called
      <i>packed</i> structure. (No pun between packet and packed.)</p>

      <p class="block">The <tt>.dev</tt> member contains the device number on
      which the event occurred. Since both the master and the slave side of a
      pty pair can generate events, and we usually do not need the master side
      events, packets from the master sides are already dropped at the kernel
      level.</p>

      <p class="block">The device field is made up of 12 bits for the major
      number and 20 bits for the minor number, in little-endian, and has this
      very same layout on all rpldev architectures. (For reference, this 12/20
      split comes from Linux 2.6.) BSD rpldev will convert device numbers to
      this scheme. As a result, a truncation is involved, since BSD uses an
      8/24 split, so minors greater than 1048575 will be truncated. This should
      not pose a problem, as one million devices is already more than a system
      could ever have in a meaningful way.</p>

      <p class="block">The following event types exist:</p>

      <div class="indent">
        <p class="code"><tt>
        <b>enum</b> {<br />
        &nbsp; &nbsp; EVT_INIT <b>=</b> 0x69,<br />
        &nbsp; &nbsp; EVT_OPEN <b>=</b> 0x01,<br />
        &nbsp; &nbsp; EVT_READ <b>=</b> 0x02,<br />
        &nbsp; &nbsp; EVT_WRITE <b>=</b> 0x03,<br />
        &nbsp; &nbsp; EVT_IOCTL <b>=</b> 0x04,<br />
        &nbsp; &nbsp; EVT_CLOSE <b>=</b> 0x05,<br />
        &nbsp; &nbsp; EVT_DEINIT <b>=</b> 0x64,<br />
        };</tt></p>
      </div>

      <p class="block"><tt>EVT_INIT</tt> is generated is generated when the
      tty is initialized. This usually happens when you <tt>open()</tt> it the
      first time.</p>

      <p class="block"><tt>EVT_OPEN</tt>, <tt>EVT_READ</tt>,
      <tt>EVT_WRITE</tt> and <tt>EVT_CLOSE</tt> are generated whenever an
      <tt>open()</tt>, <tt>read()</tt>, <tt>write()</tt> or <tt>close()</tt>,
      respectively, is done.</p>

      <p class="block"><tt>EVT_DEINIT</tt> is generated when the tty is about
      to be deinitialized. In theory, you do not know when a device will do
      this (i.e. deferred deinit). However, you've got the Kernel sources, and
      as such know when it actually happens (e.g. when the last open
      filedescriptor is closed).</p>

      <p class="block"><tt>EVT_IOCTL</tt> is generated when <tt>ioctl()</tt>
      is called on a tty. The data will consist of one <tt>uint32_t</tt>
      describing the <tt>cmd</tt> parameter. (See <tt>ioctl(2)</tt>.)</p>

      <p class="block">During an <tt>open()</tt> on a tty line (Linux only),
      the filename of the accessed dentry is extracted and passed to
      <i>rpld</i> within the payload, for convenience, so that <i>rpld</i> does
      not need to do a time-consuming search for it in <tt>/dev</tt>.</p>

    </td>
  </tr>
<?php include_once("_footer.php"); ?>
