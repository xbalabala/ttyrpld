<?php include_once("_header.php"); ?>
  <tr>
    <td align="right" valign="top" class="section"><b>Name&nbsp;&gt;</b></td>
    <td valign="top">ttyreplay - realtime log file player</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top"
    class="section"><b>Synopsis&nbsp;&gt;</b></td>

    <td valign="top">

      <p><tt><b>ttyreplay</b> [<b>--no-pctrl</b>] [<b>-F</b>|<b>-f</b>]
      [<b>-J</b> <i>time</i>|<b>-j</b> <i>packets</i>] [<b>-S</b>
      <i>factor</i>] [<b>-T</b>] [<b>-m</b> <i>msec</i>] [<b>-t</b>]
      [<b>-?</b>|<b>--help</b>]</tt></p>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top"
    class="section"><b>Description&nbsp;&gt;</b></td>

    <td valign="top">

      <p class="block"><i>ttyreplay</i> is the tool to see&nbsp;-- to
      replay&nbsp;-- what has been captured by (the Kernel module and)
      <i>rpld</i>. Give it any number of files you want to see again. Options
      apply to all files.</p>

      <p class="block">Without any arguments, <i>ttyreplay</i> will try to play
      STDIN if it is not a tty. The special file "<tt>-</tt>" (so-called
      <i>lone dash</i>) can be used to indicate STDIN, if STDIN is not the only
      file to play.</p>

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
          <td valign="top" nowrap="nowrap"><b>--no-pctrl</b></td>

          <td valign="top">Disable play control. When play control is enabled,
          you can pause replaying, skip forwards, raise/lower speed. See below
          for more.</p>

        </tr>
        <tr>
          <td valign="top" nowrap="nowrap"><b>-F</b></td>

          <td valign="top">Live feed follow mode. Seek to the end of the file
          and tail-follow any new contents that are written to it. This does
          the same as `<tt>tail -f</tt>` does with normal text logfiles.</td>

        </tr>
        <tr>
          <td valign="top" nowrap="nowrap"><b>-J</b> <i>time</i></td>

          <td valign="top">Skim to <i>time</i> position before starting to
          play.</td>

        </tr>
        <tr>
          <td valign="top" nowrap="nowrap"><b>-S</b> <i>factor</i></td>

          <td valign="top">Plays the file back with a time warp of
          <i>factor</i>. <tt>1.0</tt> is normal time, <tt>2.0</tt> is twice as
          fast, <tt>0.5</tt> half speed, etc.</td>

        </tr>
        <tr>
          <td valign="top" nowrap="nowrap"><b>-T</b></td>

          <td valign="top">Shows (replays) the log without any delays. This can
          be used to generate a screen log like <tt>script</tt> or
          <tt>screen</tt> (rpl logfile without any packet headers, basically)
          for easy grepping.</td>

        </tr>
        <tr>
          <td valign="top" nowrap="nowrap"><b>-f</b></td>

          <td valign="top">Catch-up follow mode. Plays the file back from the
          start as usual and switches into <tt>-F</tt> mode when EOF is
          reached.</td>

        </tr>
        <tr>
          <td valign="top" nowrap="nowrap"><b>-j</b> <i>count</i></td>

          <td valign="top">Skim <i>count</i> packets before starting to
          play.</td>

        </tr>
        <tr>
          <td valign="top" nowrap="nowrap"><b>-m</b> <i>msec</i></td>

          <td valign="top">Set the maximum delay in milliseconds that will be
          executed between packets. This check is done after the <tt>-S</tt>
          option, so <tt>-m1000 -S2</tt> will first crunch a delay request
          (from the logfile) by factor 2, and then see if it is more than 1
          second.</td>

        </tr>
        <tr>
          <td valign="top" nowrap="nowrap"><b>-t</b></td>

          <td valign="top">Shows the timestamp at the top right corner. Note
          that this might not work with all terminals, because for one, not all
          support it, and second, the escape code is currently hardcoded (no
          terminfo use), so it may break on some strange terminal types.</p>

          <p class="block"><tt>-t</tt> will use "<tt>%H:%M:%S</tt>" as the
          format, while <tt>-tt</tt> (or <tt>-t -t</tt>) uses "<tt>%d.%m
          %H:%M</tt>". The latter is useful for users on a tty which have been
          logged in across a day boundary.</p>

          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>Player
      control&nbsp;&gt;</b></td>
    <td valign="top">

      <p class="block">While <i>ttyreplay</i> is running, you can use the
      following keys to interactively instruct <i>ttyreplay</i> to do
      something:</p>

      <table>
        <tr>
          <td valign="top">(spacebar)</td>
          <td valign="top">Toggle pause</td>
        </tr>
        <tr>
          <td valign="top" nowrap="nowrap"><tt>&lt;</tt> (less than)</td>

          <td valign="top">Move to the previous file. Note that this might not
          work if the previous one was a pipe, STDIN or similar, because such
          have been emptied already.</td>

        </tr>
        <tr>
          <td valign="top" nowrap="nowrap"><tt>&gt;</tt> (greater than)</td>
          <td valign="top">Move to the next file</td>
        </tr>
        <tr>
          <td valign="top" nowrap="nowrap"><tt>y</tt> or <tt>z</tt></td>
          <td valign="top">Multiply playing speed by factor 0.75</td>
        </tr>
        <tr>
          <td valign="top"><tt>x</tt></td>
          <td valign="top">Normal playing speed</td>
        </tr>
        <tr>
          <td valign="top"><tt>c</tt></td>
          <td valign="top">Multiply playing speed by factor 1.33</td>
        </tr>
        <tr>
          <td valign="top"><tt>[</tt></td>
          <td valign="toP">Decrease playing speed by 0.10</td>
        </tr>
        <tr>
          <td valign="top"><tt>]</tt></td>
          <td valign="top">Increase playing speed by 0.10</td>
        </tr>
        <tr>
          <td valign="top">(keypad) <tt>6</tt></td>
          <td valign="top">Skip forward 10 seconds</td>
        </tr>
        <tr>
          <td valign="top">(keypad) <tt>9</tt></td>
          <td valign="top">Skip forward 60 seconds</td>
        </tr>
        <tr>
          <td valign="top" nowrap="nowrap"><tt>q</tt> or <tt>Ctrl+C</tt></td>
          <td valign="top">Quit</td>
        </tr>
        <tr>
          <td valign="top"><tt>e</tt></td>
          <td valign="top">Toggle ECHO mode, i.e. show <tt>EVT_READ</tt>
            packets (keypresses) as well, rather than just screen output. This
            is useful for echo-less (mostly password) prompts.</td>
        </tr>
      </table>

      <p class="block">Skipping backwards is not implemented and is even
      impossible on pipes.</p>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>Example
      files&nbsp;&gt;</b></td>
    <td valign="top">

      <p class="block">There is an example file I provided (see
      <tt>ttyrpld-examples.tbz2</tt> on the webserver), you can watch it with
      <tt>bzip2 -cd Commenting_ovcorr.bz2 | ttyreplay</tt>. It shows 1:1 (and
      in full color!) how I commented the <tt>usleep_ovcorr()</tt> function. 
      It is advisable to run it with speed factor <tt>3.0</tt> (<tt>-S</tt>
      option) and maximum delay 1 second (<tt>-m 1000</tt>), because in
      real-time, it takes approximately 30 minutes thanks to the idleness of
      the creator <tt>;-)</tt></p>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top" class="section"><b>See
    also&nbsp;&gt;</b></td>

    <td valign="top" class="block"><a href="man_rpl.5.php"><tt>rpl(5)</tt></a>,
    <a href="man_rpld.8.php"><tt>rpld(8)</tt></a></td>

  </tr>
<?php include_once("_footer.php"); ?>
