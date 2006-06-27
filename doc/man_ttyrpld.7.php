<?php include_once("_header.php"); ?>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>Name&nbsp;&gt;</b></td>
    <td valign="top">
      <p>TTYRPLD Architecture</p>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top" class="section"><b>First stage - Kernel
    Patch&nbsp;&gt;</b></td>

    <td valign="top">

      <p class="block">Different keyloggers take different ways. <i>Linspy</i>
      (see Phrack 50, File 5) modifies the syscall table to provide its own
      functions, <i>vlogger</i> (Phrack 59, File 0x0E) chooses to intercept the
      <tt>receive_buf()</tt> function. <i>ttyrpld</i>'s technique arose out of
      2 lines of <i>UML</i> (User Mode Linux) code which I found in <a
      href="http://jengelh.hopto.org/linux-suse.php"
      target="_blank">2.4.21-SUSE</a>. UML only had tty logging within, well... 
      UML, and it did not look that pretty to me. On top, UML had it easy,
      because the host system accounted for writing it to disk, but what's the
      parent of a real Kernel? None!</p>

      <p class="block">The first step in capturing the data off a tty is
      directly in the kernel, within <tt>drivers/char/tty_io.c</tt>. It is
      easier than it sounds, actually, I was pretty much driven by the two
      lines of UML code, as stated above. I did not take any further action
      within that file, but merely duplicated what UML already had in, yet more
      flexible.</p>

      <p class="block">Seven variable function pointers&nbsp;--
      <tt>rpl_init</tt>, <tt>rpl_open</tt>, <tt>rpl_read</tt>,
      <tt>rpl_write</tt>, <tt>rpl_close</tt>, <tt>rpl_deinit</tt> and
      <tt>rpl_ioctl</tt>&nbsp;-- are exported from <tt>tty_io.c</tt> for module
      hook-up. That means that a module has to redirect them to its own
      functions, when it shall go into action. That way, there is no overhead
      (read: (usually) 2 CPU instructions) when rpldev logging is not
      active.</p>

      <div class="indent">
        <p class="block"><i>Excerpt from <tt>drivers/char/tty_io.c</tt></i></p>
        <p class="code"><tt>
        <b>#</b>ifdef CONFIG_RPLDHK<br />
        <b>#</b> &nbsp; &nbsp;include &lt;linux/km_rpldev.h&gt;<br />
        <b>int (*</b>rpl_init<b>)</b>(<b>struct</b> tty_struct <b>*</b>, <b>struct</b> tty_struct <b>*</b>);<br />
        <b>int (*</b>rpl_open<b>)</b>(<b>struct</b> tty_struct <b>*</b>, <b>struct</b> tty_struct <b>*</b>);<br />
        <b>int (*</b>rpl_read<b>)</b>(<b>const char __user *</b>, <b>size_t</b>, <b>struct</b> tty_struct <b>*</b>);<br />
        <b>int (*</b>rpl_write<b>)</b>(<b>const char __user *</b>, <b>size_t</b>, <b>struct</b> tty_struct <b>*</b>);<br />
        <b>int (*</b>rpl_close<b>)</b>(<b>struct</b> tty_struct <b>*</b>, <b>struct</b> tty_struct <b>*</b>);<br />
        <b>int (*</b>rpl_deinit<b>)</b>(<b>struct</b> tty_struct <b>*</b>, <b>struct</b> tty_struct <b>*</b>);<br />
        <b>int (*</b>rpl_ioctl<b>)</b>(<b>struct</b> tty_struct <b>*</b>, <b>struct</b> tty_struct <b>*</b>, <b>unsigned int</b>, <b>unsigned long</b>);<br />
        <b>#</b>endif</tt></p>

        <p class="code"><tt>
        <b>#</b>ifdef CONFIG_RPLDHK<br />
        <b>if</b>(rpl_write <b>!=</b> NULL)<br />
        &nbsp; &nbsp; rpl_write(buf, i, tty);<br />
        <b>#</b>endif</tt></p>
      </div>

      <p class="block"><tt>tty_write()</tt>, etc. is the spot to take (as UML
      did), because this is directly below before the tty buffers spread into
      their disciplines. That way, any tty (teletype) is logged, <tt>vc</tt>
      (virtual console, aka Ctrl+Alt+F1), <tt>pts</tt> (Unix98 pty, used e.g.
      with SSH), <tt>ttyp</tt> (BSD ptys, rarely used anymore), <tt>ttyS</tt>
      (serial), and if someone's gonna try it, even weirdo stuff like
      <tt>ttyI</tt> (isdn).</p>

      <p class="block"><i>Update (2004-08-14):</i> Yes, it actually records
      serial lines too... I just found the data from an UPS <tt>;-)</tt></p>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top" class="section"><b>Second stage - Kernel
    Module&nbsp;&gt;</b></td>

    <td valign="top">

      <p class="block">The 2nd stage consists of the functions behind the
      <tt>rpl_*</tt> variables, the functions as present in the module. They
      copy the data "captured" (let's say it was passed voluntarily) by the tty
      driver to a buffer, so that <tt>rpl_open()</tt> can return as soon as
      possible to not block the tty driver.</p>

      <p class="block">This can get a little problematic if you have a lot of
      <i>simultaneous</i> users entering or producing a lot of text. I am not
      out to the disk's speed and/or the log size, but the module buffer size
      where the tty data is temporarily copied to before it is passed on.
      There is only one global ring buffer for all ttys, and I do not see the
      use of one buffer per tty as useful, for this would raise the memory and
      CPU overhead. Overall, the whole RPL structure speaks a compact binary
      protocol to minize overhead of data passing. See <a
      href="man_rpldev.4.php">rpldev(4)</a> for details on the protocol.</p>

      <div class="indent">
        <p class="block"><i>Excerpt from <tt>k_linux-2.6/rpldev.c</tt></i></p>

        <p class="code"><tt>
        <b>static int</b> krpl_write(<b>const char</b> __user <b>*</b>buf, <b>size_t</b> count, <b>struct</b> tty_struct <b>*</b>tty)&nbsp;{<br />
        &nbsp; &nbsp; <b>struct</b> rpld_packet p;<br />
        <br />
        &nbsp; &nbsp; SKIP_PTM(tty);<br />
        &nbsp; &nbsp; if(count == 0) return 0;<br />
        <br />
        &nbsp; &nbsp; p.dev &nbsp; = TTY_DEVNR(tty);<br />
        &nbsp; &nbsp; p.size &nbsp;= cpu_to_le16(count);<br />
        &nbsp; &nbsp; p.event = EVT_READ;<br />
        &nbsp; &nbsp; p.magic = MAGIC_SIG;<br />
        &nbsp; &nbsp; fill_time(&p.time);<br />
        &nbsp; &nbsp; <b>return</b> circular_put_packet(&amp;p, buf, count);<br />
        }</tt></p>
      </div>

      <p class="block">The default buffer size of 32K has proven to be enough
      for a small network. If it does not suffice, you can raise the buffer
      size. Thanks to <i>rpldev</i> being a module, you can remove it and
      re-<tt>modprobe</tt> with the corresponding parameter
      <tt>Bufsize</tt>.</p>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top" class="section"><b>Third stage - Userspace
    Gateway&nbsp;&gt;</b></td>

    <td valign="top">

      <p class="block">The module provides a character-based device node,
      <tt>/dev/rpl</tt> (or <tt>/dev/misc/rpl</tt>) to read from. Device and
      userspace logging daemon must use the same protocol for the data passed
      over the channel.</p>

      <p class="block">As far as <i>ttyrpld</i> is concerned, its kernel module
      <i>rpldev</i> allocates the buffer and hooks up on the <tt>rpl_*</tt>
      function pointers when the device is successfully <i>opened</i>, not when
      the module is loaded. This saves us an unnecessary call to, say,
      <tt>kio_write()</tt> if it would anyway just return because of an
      <tt>if()</tt> condition for checking if the device is open. (That means,
      I did not want to have a global variable indicating the device is opened
      and having an <tt>if()</tt> within <tt>kio_*()</tt>.)</p>

      <p class="block">Similar applies when closing the device, the memory is
      released and the function hooks are set back. This saves memory and also
      CPU time, because an early <tt>if()</tt> within <tt>tty_io.c</tt> will
      skip everything when no one is reading <tt>/dev/rpl</tt>.</p>

      <div class="indent">
        <p class="block"><i>Excerpt from <tt>kernel-2.6/rpldev.c</tt></i></p>

        <p class="code"><tt>
        <b>static ssize_t</b> urpl_read(<b>struct</b> file <b>*</b>filp, <b>char *</b>buf, <b>size_t</b> count, <b>loff_t *</b>ppos)&nbsp;{<br />
        &nbsp; &nbsp; ...<br />
        &nbsp; &nbsp; <b>// Data is available, so give it to the user</b><br />
        &nbsp; &nbsp; count = imin(count, avail_R());<br />
        &nbsp; &nbsp; mv_to_user(buf, count);<br />
        &nbsp; &nbsp; ...<br />
        &nbsp; &nbsp; <b>return</b> count;<br />
        }</tt></p>
      </div>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top" class="section"><b>Fourth stage - Userspace
    Logging Daemon&nbsp;&gt;</b></td>

    <td valign="top">

      <p class="block">A userspace daemon reads, evaluates and stores the data
      retrieved via the device.</p>

      <p class="block">The reasons why I think this design is good are
      that:</p>

      <ul>

        <li>Only the real necessary stuff is compiled <i>into</i> the Kernel,
          everything else goes as a <i>module</i>.</li>

        <li>(This goes in accordance with the above even if it sounds weird:)
          Only the real necessary stuff is done <i>within</i> Kernel space,
          everything else is put into user-space.</li>

        <li>The amount of changes you need to apply when modifying one stage.
          You could change the inner working of the module, change the way the
          device responds to the logging daemon, etc. without needing to change
          too much.</li>

        <li>You can implement something in <i>rpld</i> while not changing any
          Kernel components.</li>

        <li>User memory can be swapped out if is not used, Kernel memory can
          not. (Though, <i>rpld</i> locks the buffer in memory, heh heh.) If
          there is no tty activity, the logging daemon will not become active
          and thus can be swapped out to give other applications some more
          physical memory.</li>

        <li>Do whatever you like. For example, compress the records, userspace
          is open, because you can link all your favorite libraries.</lI>

      </ul>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top" class="section"><b>"Fifth stage" - Replaying
    logs&nbsp;&gt;</b></td>

    <td valign="top">

      <p class="block"><i>ttyreplay</i> is that thing to analyze the logs
      stored by <i>rpld</i>. Thanks to the time-stamping of <i>rpld</i> and the
      1:1 passing of arbitrary data allows <i>ttyreplay</i> to show what has
      happened on the chosen terminal in real-time, and exactly as the original
      user saw it. (Objection: You need to use the same terminal type and size,
      because also the ANSI codes are reprinted as-is.)</p>

      <p class="block">Being able to play it back in real-time also requires a
      fine timer for the delays between two keystrokes&nbsp;/ data packets. I
      found out that delays have a maximum precision (in the
      <tt>SCHED_OTHER</tt> policy domain), so I invented some kind of algorithm
      to bypass this visible effect for the user:</p>

      <p class="block">It's about the "delay overhead correction algorithm".
      The maximum delay precision for user-space applications within
      <tt>SCHED_OTHER</tt> is <tt>1/HZ</tt> seconds. (See
      <tt>linux/include/asm/param.h</tt>). So when wanting a 5000 microsecond
      delay, the real time we are waiting is between 10k to 15k µs. To get
      around this, the algorithm checks the time it has actually spent for a
      particular delay. For more details, please see the
      <tt>usleep_ovcorr()</tt> function in <tt>user/replay.c</tt> which has
      been commented thoroughly.</p>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top" class="section"><b>See
    also&nbsp;&gt;</b></td>

    <td valign="top" class="block"><a
    href="man_ttyreplay.1.php"><tt>ttyreplay(1)</tt></a>, <a
    href="man_rpldev.4.php"><tt>rpldev(4)</tt></a>, <a
    href="man_rpld.8.php"><tt>rpld(8)</tt></a></td>

  </tr>
<?php include_once("_footer.php"); ?>
