<?php include_once("_header.php"); ?>
  <tr>
    <td align="right" valign="top" class="section"><b>Name&nbsp;&gt;</b></td>
    <td valign="top">rplctl - control rpld</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top"
    class="section"><b>Synopsis&nbsp;&gt;</b></td>

    <td valign="top">

      <p><tt><b>rplctl</b> [<b>-A</b> <i>tty</i>] [<b>-D</b> <i>tty</i>]
      [<b>-L</b> [<i>tty</i>]] [<b>-S</b> <i>tty</i>] [<b>-X</b> <i>tty</i>]
      [<b>-Z</b> [<i>tty</i>]] [<b>-f</b> <i>socket</i>] [<b>-t</b>]</tt></p>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top"
    class="section"><b>Description&nbsp;&gt;</b></td>

    <td valign="top">

      <p class="block">The <tt>rplctl</tt> utility is used to query and control
      <i>rpld</i> (and its <i>infod</i> subcomponent). It will print info about
      the requested ttys, or all ttys currently monitored or optionized if no
      arguments are given.</p>

      <p class="block"><i>rplctl</i> will read the <tt>INFOD_PORT</tt> variable
      from <tt>rpld.conf</tt> by default, if not overriden by the <tt>-f</tt>
      option.</p>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top"
    class="section"><b>Options&nbsp;&gt;</b></td>

    <td valign="top">

      <table>
        <tr>
          <td valign="top" nowrap="nowrap"><tt><b>-A</b> <i>tty</i></tt></td>

          <td valign="top" class="block">Activate logging for the given tty.
          It can be a filename (which must exist) or a device number using
          "<tt>=MAJOR,MINOR</tt>".</td>

        </tr>
        <tr>
          <td valign="top" nowrap="nowrap"><tt><b>-D</b> <i>tty</i></tt></td>

          <td valign="top" class="block">Deactivate logging for the given
          tty.</td>

        </tr>
        <tr>
          <td valign="top" nowrap="nowrap"><tt><b>-L</b> [<i>tty</i>]</td>

          <td valign="top" class="block">Explicitly request a listing of all
          ttys currently active or have option set. If a <i>tty</i> is given,
          only display info for that particular one.</td>

        </tr>
        <tr>
          <td valign="top" nowrap="nowrap"><tt><b>-S</b> <i>tty</i></tt></td>

          <td valign="top" class="block">Deactivate logging for the given tty
          until it is deinitialized.</td>

        </tr>
        <tr>
          <td valign="top" nowrap="nowrap"><tt><b>-X</b> <i>tty</i></tt></td>

          <td valign="top" class="block">Call <tt>log_close()</tt> for the
          given tty. <i>rpld</i> will close the logfile, and necessarily open
          up a new one whenever new activity is detected (and logging this
          terminal is not deactivated) on that terminal.</td>

        </tr>
        <tr>
          <td valign="top" nowrap="nowrap"><tt><b>-Z</b> [<i>tty</i>]</tt></td>

          <td valign="top" class="block">Zero all packet and byte counters,
          both of <i>rpld</i> and every single tty. If a <i>tty</i> is given,
          only zeroes the stats of that tty.</td>

        </tr>
        <tr>

          <td valign="top" nowrap="nowrap"><tt><b>-f</b>
          <i>socket</i></tt></td>

          <td valign="top" class="block">Path to the <i>infod</i> socket to
          connect to. The default is <tt>/var/run/.rplinfo_socket</tt> or
          <tt>INFOD_PORT</tt> in the <tt>rpld.conf</tt> configuration
          file.</td>

        </tr>
        <tr>
          <td valign="top" nowrap="nowrap"><tt><b>-t</b></td>

          <td valign="top" class="block">Generate output suitable for
          <tt>sscanf()</tt>, <tt>split()</tt> or anything that deals best with
          simple text strings. See below for details on the output format.</td>

        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top" class="section"><b>Example
    output&nbsp;&gt;</b></td>

    <td valign="top">

    <p class="block">A command like `<tt>rplctl</tt>` could output this:</p>

    <div class="indent">
      <p class="code"><tt>
A TTY &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;BYTES IN &nbsp; &nbsp; &nbsp; OUT FILENAME<br />
==========================================================================<br />
&nbsp; * &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;2159 &nbsp; &nbsp; 81129 ttyrpld 2.12/Linux<br />
&nbsp; IOCD: 0/118/117/0 &nbsp;RW: 1851/1396 &nbsp;I: 0 &nbsp;B: 0<br />
--------------------------------------------------------------------------<br />
D tty7 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;937 &nbsp; &nbsp; &nbsp; &nbsp; 0 root/20050328.224208.tty7<br />
D pts-1 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 0 &nbsp; &nbsp; &nbsp; 586 boinc/20050328.224219.pts-1<br />
D pts-2 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 0 &nbsp; &nbsp; &nbsp;5812 root/20050328.224208.pts-2<br />
D pts-3 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 358 &nbsp; &nbsp; 22113 root/20050328.224208.pts-3<br />
D pts-4 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 864 &nbsp; &nbsp; 52618 root/20050328.224341.pts-4<br />
--------------------------------------------------------------------------
      </tt></p>
    </div>

    <p class="block">The status field (<tt>A</tt>) can be one of A, D or S:
    <b>A</b>ctivated, <b>D</b>eactivated, Deactived for this
    <b>S</b>ession.</p>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top" class="section"><b>sscanf()
    format&nbsp;&gt;</b></td>

    <td valign="top">

    <p class="block">On the other hand, `<tt>rplctl -t</tt>` could produce
    this:</p>

    <div class="indent">
      <p class="code"><tt>
ttyrpld 2.12<br />
format 3<br />
0 144 143 0 3347 2496 3756 122881 0 0<br />
D 4 7 tty7 1507 0 /var/log/rpl/root/20050328.224208.tty7<br />
D 136 1 pts-1 0 1241 /var/log/rpl/boinc/20050328.224219.pts-1<br />
D 136 2 pts-2 0 7585 /var/log/rpl/root/20050328.224208.pts-2<br />
D 136 3 pts-3 365 22798 /var/log/rpl/root/20050328.224208.pts-3<br />
D 136 4 pts-4 1884 91257 /var/log/rpl/root/20050328.224341.pts-4<br />
      </tt></p>
    </div>

    <p class="block">The first line of the "parseable" output (<tt>-t</tt>) is
    the program used. It can (and should) be ignored by programs querying
    <i>infod</i>. The second is the status line about <i>rpld</i>. The
    <tt>sscanf</tt> formula is "<tt>%lu %lu %lu %lu %llu %llu %lu %lu</tt>".
    The fields are (in this order):</p>

    <ul>
      <li><tt>EVT_INIT</tt>&nbsp;-- tty first opens</li>
      <li><tt>EVT_OPEN</tt>&nbsp;-- number of <tt>open()</tt> operation on any tty</li>
      <li><tt>EVT_CLOSE</tt>&nbsp;-- number of <tt>close()</tt> operations on any tty</li>
      <li><tt>EVT_DEINIT</tt>&nbsp;-- tty deallocation</li>
      <li><tt>EVT_READ</tt>&nbsp;-- packets read from <i>rpldev</i></li>
      <li><tt>EVT_WRITE</tt>&nbsp;-- packets read from <i>rpldev</i></li>
      <li><tt>EVT_READ</tt>&nbsp;-- payload bytecount</li>
      <li><tt>EVT_WRITE</tt>&nbsp;-- payload bytecount</li>
      <li><tt>EVT_IOCTL</tt> packets received</li>
      <li>packets considered bad (i.e. bad magic)&nbsp;-- might be more than
        there are actually damaged, because the algorithm needs to find
        <i>something</i> that looks good.</li>
    </ul>

    <p class="block">All other lines are tty information lines, whose formula
    is "<tt>%c %ld %ld %s %llu %llu %s</tt>", and the fields are:</p>

    <ul>
      <li>status&nbsp;-- <tt>'A'</tt> for activated, <tt>'D'</tt> is deactivated and
        <tt>'S'</tt> means deactivated until session ends.</li>
      <li>major number</li>
      <li>minor number</li>
      <li>mnemonic string for the major/minor number</li>
      <li><tt>EVT_READ</tt> payload bytecount</li>
      <li><tt>EVT_WRITE</tt> payload bytecount</li>
      <li>full filename being written to (this is different from the
        human-readable output which only shows it in part)</li>
    </ul>

    </td>

  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top" class="section"><b>See
    also&nbsp;&gt;</b></td>

    <td valign="top" class="block"><a
    href="man_rpld.8.php"><tt>rpld(8)</tt></a></td>

  </tr>
<?php include_once("_footer.php"); ?>
