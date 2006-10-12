<?php include_once("_header.php"); ?>
  <tr>

    <td align="right" valign="top"
    class="section"><b>Description&nbsp;&gt;</b></td>

    <td valign="top">

      <p class="block"><i>ttyrpld</i> is a mult-os kernel-level tty key- and
      screenlogger with (a)synchronous replay supprt. It supports most tty
      types, including <tt>vc</tt>, bsd and unix98-style <tt>pty</tt>s
      (xterm/ssh), serial, isdn, etc. Being implemented within the Kernel makes
      it unavoidable for the default user. It runs with no overhead if the
      logging daemon is not active.</p>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top" class="section"><b>General
      info&nbsp;&gt;</b></td>

    <td valign="top">
      <div class="indent"><table border="1" class="sBorder">
        <tr>
          <td>License:</td>
          <td><ul>
              <li>GNU Lesser General Public License (LGPL) version 2.1</li>
              <li>BSD-style (for BSD parts)</li>
            </ul></td>
        </tr>
        <tr>
          <td>Languages:</td>
          <td>English, Dutch, French, German, Italian, Norwegian-Bokmål,
            Persian(Farsi), Spanish, Swedish</td>
        </tr>
        <tr>
          <td>Operating Systems:</td>
          <td>Linux 2.4/2.6, FreeBSD 5.3/6.0/6.1, OpenBSD 3.9, NetBSD 3.0<br />
            (and possibly offsprings)</td>
        </tr>
      </table></div>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top" class="section"><b>Technical
    Aspects&nbsp;&gt;</b></td>

    <td valign="top">

      <p><i>... which cause it to differ from existing solutions:</i></p>

      <p class="block">Most other solutions deploy the logging completely in
      userspace and make it dependent on the user to activate the logging. 
      Definitely, an intruder does not start e.g. <tt>/usr/bin/script</tt>
      voluntarily. I have only seen few other kernel-based loggers. One of them
      is outdated, others query the X86 keyboard driver using interrupt
      hijacking and fixed translation. This is very unportable, because it only
      catches raw AT keyboard scancodes, but not USB or even other keyboards
      (unless they are in compatibility mode), and it does not work at all for
      network traffic.</p>

      <p class="block">Only the FreeBSD snooper <i>watch</i>, which operates on
      the <tt>/dev/snp*</tt> devices, can be taken for a comparison. However,
      it can only do the interactive live feed mode, no logging is possible for
      later replay. Packet time stamps are also missing.</p>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td align="right" valign="top"
    class="section"><b>Components&nbsp;&gt;</b></td>

    <td valign="top">

      <p class="block">This kit (<i>ttyrpld</i>) consists of four
      components:</p>

      <p class="block"><i>kpatch</i>: The Kernel patch adds a few lines to
      provide the <i>rpldev</i> extension hooks, which (any) module can then
      get onto.</p>

      <p class="block"><i>rpldev</i>: The Kernel module is responsible for
      grabbing the data off the tty line and providing a character device for
      the user-space logging daemon. Data grabbed of the tty is directly passed
      to the overlying daemons, so with the correct terminal settings you can
      get a 1:1 replay.</p>

      <p class="block">For systems where module loading is not possible or not
      widely supported (OpenBSD for example), <i>rpldev</i> is integrated into
      the kpatch.</p>

      <p class="block"><i>rpld</i>: Having received the captured data, the
      logging daemon can store them in any format and/or facility, with or
      without compression, just as it likes, for this happens in user-space and
      thus you have all the fluffy libraries available. (That would not be the
      case from Kernel space.)</p>

      <p class="block"><i>ttyreplay</i>: real-time log analyzer. Think of it as
      a simple video player.</p>

    </td>
  </tr>
<?php include_once("_footer.php"); ?>
