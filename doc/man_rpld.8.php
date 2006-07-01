<?php include_once("_header.php"); ?>
  <tr>
    <td align="right" valign="top" class="section"><b>Name&nbsp;&gt;</b></td>
    <td valign="top">rpld - tty logging daemon</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top"
    class="section"><b>Synopsis&nbsp;&gt;</b></td>

    <td valign="top">

      <p><tt><b>rpld</b> [<b>-D</b> <i>dev</i>] [<b>-I</b>] [<b>-O</b>
      <i>ofmt</i>] [<b>-Q</b>] [<b>-U</b> <i>user</i>] [<b>-c</b>
      <i>configfile</i>] [<b>-s</b>] [<b>-v</b>]</tt></p>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top"
    class="section"><b>Description&nbsp;&gt;</b></td>

    <td valign="top">

      <p class="block"><tt>rpld</tt> is the user-space daemon that reads
      <tt>/dev/rpl</tt> and plexes the data to different files depending on
      which tty they were logged. It also adds a timestamp so that replaying
      can be done in real-time.</p>

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

          <td valign="top" nowrap="nowrap"><tt><b>-D</b> <i>dev</i></tt></td>

          <td valign="top" class="block">Path to the rpl device, e.g.
          <tt>/dev/rpl</tt>.</td>

        </tr>
        <tr>
          <td valign="top" nowrap="nowrap"><tt><b>-I</b></tt></td>

          <td valign="top" class="block">Start the <i>infod</i> component if it
          is not automatically started when the <tt>INFOD_START</tt>
          configuration variable is set.</td>

        </tr>
        <tr>

          <td valign="top" nowrap="nowrap"><tt><b>-O</b>
          <i>string</i></tt></td>

          <td valign="top" class="block">Overrides the hardcoded and
          configuration file values (processed so far) for the log file naming
          scheme. You can use the printf-like placeholders explained below.
          Subsequent <tt>-c</tt> may override this, if an <tt>OFMT</tt>
          variable is found in further configuration files. Make sure the user
          can create files according to <tt>-O</tt>/<tt>OFMT</tt>.</td>

        </tr>
        <tr>
          <td valign="top" nowrap="nowrap"><tt><b>-Q</b></tt></td>

          <td valign="top" class="block">All ttys start in deactivated mode
          (rather than activated). They can then be activated when needed.
          Byte-counting is done in either case.</td>

        </tr>
        <tr>
          <td valign="top" nowrap="nowrap"><tt><b>-U</b> <i>user</i></tt></td>

          <td valign="top" class="block">Drop all privilegues and change to
          <i>user</i> after initialization (memory lock, creating and opening
          RPL device). This can be either a username or a numeric UID.</td>

        </tr>
        <tr>
          <td valign="top" nowrap="nowrap"><tt><b>-c</b> <i>file</i></tt></td>

          <td valign="top" class="block">Load configuration variables from
          <i>file</i>. The in-memory copies of the variables are not changed if
          no such var name is found in the file. Unknown var names in the
          configuration file are also ignored.</td>

        </tr>
        <tr>
          <td valign="top" nowrap="nowrap"><tt><b>-s</b></tt></td>

          <td valign="top" class="block">Print warnings and error messages to
          <i>syslog</i> rather than <tt>stderr</tt>.</td>

        </tr>
        <tr>
          <td valign="top" nowrap="nowrap"><tt><b>-v</b></tt></td>

          <td valign="top" class="block">Print statistics about recorded
          packets on <tt>stdout</tt> while <i>rpld</i> is running. This option
          overrides <tt>-s</tt>.</td>

        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top" class="section"><b>Privilegue
    separation&nbsp;&gt;</b></td>

    <td valign="top">

      <p class="block"><i>rpld</i> offers the possibility to change to another
      user's identity after the initialization phase is complete, even though
      if it is not really required since there is very few to exploit (if at
      all!), and on top it's local. The default package (from
      <i>ttyrpld.sf.net</i>) uses the <tt>daemon</tt> user. You can change this
      in the configuration file.</p>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top"
    class="section"><b>Logging&nbsp;&gt;</b></td>

    <td valign="top">

      <p class="block"><i>rpld</i> does not detach itself to help debugging,
      but using the <tt>startproc</tt>, <tt>setsid</tt> starter tools or bash's
      <tt>disown</tt> builtin can help bringing it in the background, if you
      need to. The only time when the daemon will output something is either
      during initialization or when there is really trouble, like memory
      allocation failure. In either case, error messages are rare and you would
      not need to worry about redirecting <tt>stderr</tt>.</p>

      <p class="block">You can run <i>rpld</i> with the <tt>-v</tt> option to
      enable printing statistics on <tt>stdout</tt>. Even if you do not have
      <tt>-v</tt> specified, you can send <i>rpld</i> a <tt>SIGALRM</tt> signal
      to make it print the current statistics. That of course only makes sense
      when <tt>stdout</tt> is connected to something but <tt>/dev/null</tt> or
      <tt>/dev/zero</tt>. You can send it multiple <tt>SIGALRM</tt>s, of
      course, but you should consider using <tt>-v</tt> then, maybe. The
      statistics are printed with the move-to-beginning-of-line character
      (<tt>\r</tt>), which is not that suitable for logging, though.</p>

      <p class="block">Basically, every tty is monitored, but certain kinds are
      excluded, like the master sides of BSD (major number 2 (Linux), 6 on BSD)
      and Unix98 ptys (major number 128), because they are just a mirror of
      their slave sides with things turned around and are rarely useful. (On
      BSD, they are not explicitly excluded because they do not seem to
      generate any data.)</p>

      <p class="block"><i>rpld</i> will divert logging of a tty to a new file
      when the tty inode is opened the next time and if the owner of it has
      changed. This will make logins on <tt>/dev/tty</tt><i>N</i> go to the
      right file. Note that `<tt>su</tt>` does not change the ownership, so
      both sessions (normal and <tt>su</tt>'ed) will go into the same file. 
      (And that's good, because it then logs by "real person" rather than login
      name.) The byte count statistics are zeroed upon change detection.</p>

  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top" class="section"><b>Configuration
    file&nbsp;&gt;</b></td>

    <td valign="top">

      <p class="block"><i>rpld</i> starts with its hardcoded defaults, then
      tries to read <tt>/etc/rpld.conf</tt> and finally <tt>rpld.conf</tt> in
      the directory where the <tt>rpld</tt> binary is in.</p>

      <p class="block">Configuration files have a simple <tt>KEY=VALUE</tt>
      syntax. Empty lines, lines beginning with a hash mark (<tt>#</tt>) or
      unrecognized keys are ignored.</p>

      <table>
        <tr>
          <td valign="top"><tt>DEVICE</tt></td>

          <td valign="top" class="block">List of possible rpl devices,
          separated by colon (<tt>:</tt>). The default is
          "<tt>/dev/misc/rpl:/dev/rpl</tt>".</td>

        </tr>
        <tr>
          <td valign="top"><tt>OFMT</tt></tt>

          <td valign="top"><p class="block"><tt>OFMT</tt> combines the
          directory to write the tty recordings into (relative to the working
          directory of <i>rpld</i>) and the name of the log files. Note that
          the working directory of <i>rpld</i> depends on where it was started
          from, e.g. with <tt>startproc</tt>, the CWD is mostly the root path
          (<tt>/</tt>). It is wise to use absolute paths here. The following
          printf-style tags can be used in <tt>OFMT</tt>:</p>

          <p class="block"><tt>%d</tt>&nbsp;-- Time when tty was opened
          (usually when someone logs in or an xterm was opened). In
          <tt>YYYYMMDD</tt> format.</p>

          <p class="block"><tt>%l</tt>&nbsp;-- Terminal (line) the user logged
          on.  The string is taken from a string database. Typical strings are
          <tt>tty*</tt> for virtual consoles, <tt>pts-*</tt> for
          pseudo-terminals, <tt>ttyS*</tt> for serial lines. If there is no
          matching string entry, the device number is used in square brackets,
          e.g. <tt>[240:0]</tt>.</p>

          <p class="block"><tt>%u</tt>&nbsp;-- User who owned the tty when it
          was opened. If the device could not be <tt>stat()</tt>'ed,
          <tt>%u</tt> will be substituted by "<tt>NONE</tt>". If the UID could
          not be translated into a name, the UID is spit out instead.</p>

          <p class="block"><i>Directories will be created as needed</i> (if
          permission allows). Thus, you can have
          <tt>/var/log/rpl/%u/%d.%t.%l</tt>, and it will automatically create
          <tt>%u</tt>, provided that <tt>/var/log/rpl</tt> is writable for the
          user running <i>rpld</i>.</p></td>

        </tr>
        <tr>
          <td valign="top"><tt>USER</tt></p>

          <td class="block">User to change to after all initialization. Make
          sure that the user can create files according to <tt>OFMT</tt>.</td>

        </tr>
        <tr>
          <td valign="top"><tt>START_INFOD</tt></p>

          <td class="block">Starts the <i>infod</i> component.</td>
        </tr>
        <tr>
          <td valign="top"><tt>INFOD_PORT</tt></p>

          <td class="block">Specify the path of the socket which <i>infod</i>
          provides for clients. The default value is
          <tt>/var/run/.rplinfod_socket</tt>.</td>

        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top" class="section"><b>See
    also&nbsp;&gt;</b></td>

    <td valign="top" class="block"><a
    href="man_ttyreplay.1.php"><tt>ttyreplay(1)</tt></a>&nbsp;</td>

  </tr>
<?php include_once("_footer.php"); ?>
