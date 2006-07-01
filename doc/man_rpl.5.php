<?php include_once("_header.php"); ?>
  <tr>
    <td align="right" valign="top" class="section"><b>Name&nbsp;&gt;</b></td>
    <td valign="top">rpl - ttyrpld log file format</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top"
    class="section"><b>Description&nbsp;&gt;</b></td>

    <td valign="top">

      <p class="block">The logfiles <i>rpld</i> creates are a small variation
      of the packet protocol as described in <a
      href="man_rpldev.4.php">rpldev(4)</a>. The <tt>.dev</tt> field is not
      present. (Since ttyrpld v2.00, the timestamp is already added in the
      kernel.) All fields are little endian and packed, i.e. there are no
      alignment gaps. The structure is similar to the <tt>struct
      rpldev_packet</tt>:</p>

      <div class="indent">
        <p class="code"><tt>
        <b>struct</b> rpldsk_packet {<br />
        &nbsp; &nbsp; <b>uint16_t</b> size;<br />
        &nbsp; &nbsp; <b>uint8_t</b> event, magic;<br />
        &nbsp; &nbsp; <b>struct</b> timeval time;<br />
        } <i>__attribute__((packed))</i>;</tt></p>
      </div>

      <p class="block">The <tt>.magic</tt> is always <tt>0xEE</tt>.</p>
      Possible values for <tt>.event</tt> equal those listed in <a
      href="man_rpldev.4.php">rpldev(4)</a>, plus the following:</p>

      <div class="indent">
        <p class="code"><tt>
        <b>enum</b> {<br />
        &nbsp; &nbsp; EVT_MAGIC <b>=</b> 0x4A,<br />
        &nbsp; &nbsp; EVT_ID_PROG <b>=</b> 0xF0,<br />
        &nbsp; &nbsp; EVT_ID_DEVPATH <b>=</b> 0xF1,<br />
        &nbsp; &nbsp; EVT_ID_TIME <b>=</b> 0xF2,<br />
        &nbsp; &nbsp; EVT_ID_USER <b>=</b> 0xF3,<br />
        };</tt></p>
      </div>

      <p class="block">A <tt>EVT_ID_PROG</tt> is added to each logfile by
      <i>rpld</i> to contain the program with which it was created.
      (<i>rpld</i> in this case&nbsp;-- but anyone is free to write a different
      daemon.)</p>

      <p class="block">A <tt>EVT_ID_DEVPATH</tt> contains the path of the
      device node that has been tapped. There is a special handling case in
      <i>rpld</i> when multiple device nodes with the same major-minor number
      are used at the same time:</p>

      <ul>
        <li>if the first packet <i>rpld</i> picks up for a certain
          <tt>.dev</tt> line is a <tt>EVT_INIT</tt> or <tt>EVT_OPEN</tt> packet
          with a dentry name (e.g. <tt>/dev/tty1</tt>), this name will be used
          for this major-minor number</li>
        <li><i>rpld</i> will look into <tt>/dev</tt> and take the first
          pick</li>
      </ul>

      <p class="block">In the default case, you should not worry, as each
      major-minor number only has one node in <tt>/dev</tt>, and even if it
      does not, you are able to know what tty was logged, because the dentries
      are similarly-named.</p>

      <p class="block"><tt>EVT_ID_TIME</tt> packets carry the time the log
      was started. This is useful because the logfile's timestamp on the
      filesystem may change due to user interaction.</p>

      <p class="block">Last, but not least, <tt>EVT_ID_USER</tt> contains the
      username (or UID if no user was found) that was being traced. Especially
      useful when users get removed after the logs have been recorded.</p>

      <p class="block">All of these four packets are informational ones and are
      not required for proper replay with <i>ttyreplay</i>.</p>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top" class="section"><b>See
    also&nbsp;&gt;</b></td>

    <td valign="top" class="block"><a
    href="man_rpldev.4.php"><tt>rpldev(4)</tt></a>&nbsp;</td>

  </tr>
<?php include_once("_footer.php"); ?>
