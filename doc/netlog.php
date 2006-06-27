<?php include_once("_header.php"); ?>
  <tr>
    <td align="right" valign="top"
      class="section"><b>Description&nbsp;&gt;</b></td>
    <td valign="top">

      <p class="block">There is a simple way on how to scoop <tt>/dev/rpl</tt>
      data directly to another host without logging it on the one where it was
      captured. This is commonly referred to as "network logging".</p>

      <p class="block"><i>Note:</i> This does not work reliably (i.e. you get
      wrong device names) when the major/minor numbers are different between
      two hosts! Network logging is currently only of use between Linux where
      tty classes have the same major number. Another constraint is that the
      tty node must also exist on the destination system, which is not always
      the case with dynamic /dev directories (either by devfs or udev).</p>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top"
      class="section"><b>Setup&nbsp;&gt;</b></td>
    <td valign="top">

      <p class="block">Beginning with <i>ttyrpld 2.00</i>, the timestamp is
      added in kernelspace, so that you can directly <tt>cat /dev/rpl</tt> to a
      file and process it <u>later</u>. The "later" was not possible before
      ttyrpld 2.00. Therefore, the remote capture process has become extremely
      simple. Run this on the capture host:</p>

      <div class="indent">
        <p class="code"><tt># <b>ssh -Te none daemon@storage "rpld -D -"
        &lt;/dev/rpl</b></tt></p>
      </div>

      <p class="block">You might want to use a different account name for
      ssh'ing (because <tt>daemon</tt> is a system account and remote logins
      for this "user" would look like an intrusion). I also suggest using SSH
      keys so that you do not need to enter a password when you connect.</p>

    </td>
  </tr>
<?php include_once("_footer.php"); ?>
