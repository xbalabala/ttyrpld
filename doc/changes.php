<?php include_once("_header.php"); ?>
  <tr>
    <td align="right" valign="top"
      class="section"><b>Overview&nbsp;&gt;</b></td>
    <td valign="top">

      <p class="block">You do not need to repatch your kernel or recompile
      rpldev if they have not changed significantly between ttyrpld
      releases.</p>

      <div class="indent">
      <table border="1" class="sBorder">
        <tr>
          <td valign="bottom"><i>Operating system</i></td>
          <td valign="bottom"><i>last time KPATCHes changed:</i></td>
          <td valign="bottom"><i>last time rpldev changed:</i></td>
        </tr>
        <tr>
          <td>Linux</td>
          <td>2.16</td>
          <td>2.16</td>
        </tr>
        <tr>
          <td>FreeBSD</td>
          <td>2.16</td>
          <td>2.16</td>
        </tr>
        <tr>
          <td>OpenBSD</td>
          <td>2.18</td>
          <td>no-go: <i>monolithic kernel</i> / <i>modules not really supported</i></td>
        </tr>
        <tr>
          <td>NetBSD</td>
          <td>2.16</td>
          <td>2.16</td>
        </tr>
      </table>
      </div>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 2.18 (July 01
      2006)&nbsp;&gt;</td>
    <td valign="top">

      <ul type="clog">
        <li class="star">Fixed OpenBSD 3.9 kpatch</li>
        <li class="plus">Readded workaround for OpenBSD/NetBSD EINTR signal handling that got lost in 2.17</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 2.17 (June 19
      2006)&nbsp;&gt;</td>
    <td valign="top">

      <ul type="clog">
        <li class="plus">Support for Linux 2.6.17, FreeBSD 6.1, OpenBSD 3.9</li>
        <li class="plus">added missing <tt>-fprofile-arcs -ftest-coverage</tt> to <tt>LDFLAGS</tt> for <tt>PROF=1</tt></li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 2.16 (April 01
      2006)&nbsp;&gt;</td>
    <td valign="top">

      <ul type="clog">
        <li class="minus"><tt>user/rpld.c</tt>: removed a superfluous variable</li>
        <li class="star">Added polling functions to the BSD rpldevs. This fixes the high CPU usage (which is due to the implementation of pthreads on OpenBSD) observed with <i>rpld</i>/OpenBSD.</li>
        <li class="star">OpenBSD part is again just a patch since modules are said to be unsupported on non-i386</li>
        <li class="star">Due to popular request, the docs now come with a black-on-white ("paper color") color scheme and no fixed font size. The old gray-on-black ("console color") is available as an alternate stylesheet and can be used in Firefox using <i>View&nbsp;&gt; Page Style&nbsp;&gt; Black background</i>.</li>
        <li class="plus">Added the <tt>EVT_MAGIC</tt> pseudo event type</li>
        <li class="plus">Added <tt>share/ttyrpld.magic</tt> for the <tt>file(1)</tt> utility</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 2.15 (March 11 2006)&nbsp;&gt;</td>
    <td valign="top">

      <p><b>General:</b></p>
      <ul class="clog">
        <li class="plus">added support for NetBSD 3.0</li>
        <li class="star">shifts for majors<tt>&gt;255</tt> and minors<tt>&gt;255</tt> were broken in <tt>include/dev.h</tt></li>
      </ul>

      <p><b>kpatch:</b></p>
      <ul class="clog">
        <li class="star">[All] fixed some SMP race conditions</li>
        <li class="plus">[OpenBSD] fixed: <tt>rpl_deinit()</tt> was not called
          in <tt>ttyclose()</tt></li>
      </ul>

      <p><b>rpldev:</b></p>
      <ul class="clog">
        <li class="star">[All] rename functions to make function more clear (<tt>mv_to_user</tt>&nbsp;-&gt; <tt>circular_get</tt>, <tt>mv_buffer</tt>&nbsp;-&gt; <tt>circular_put_packet</tt>, <tt>mv_buffer2</tt>&nbsp;-&gt; <tt>circular_put</tt>)</li>
        <li class="star">[All] make <tt>avail_R()</tt> and <tt>avail_W()</tt> return <tt>size_t</tt> instead of <tt>ssize_t</tt>, addressing some potential overflow problems</li>
        <li class="plus">[Linux] document circulary calculation of <tt>avail_*()</tt>, please report errors</li>
        <li class="star">[FreeBSD] merged <tt>freebsd-*/rpldev.c</tt> into one file</li>
        <li class="minus">[FreeBSD/OpenBSD] removed <tt>SKIP_PTM</tt>, since master ptys do not generate any noise (that's a kpatch feature)</li>
        <li class="star">[OpenBSD] rpldev is now a module (<tt>rpldev_mod.o</tt>)</li>
        <li class="star">[OpenBSD] loads at new device number (<tt>228,0</tt>), please re-mknod your <tt>/dev/rpl</tt> node</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 2.12 (January 21 2005)&nbsp;&gt;</td>
    <td valign="top">

      <ul class="clog">
        <li class="star">fixed: FreeBSD 6.0 hands <tt>krpl_close()</tt> a <tt>NULL</tt> pointer in some cases; handle it properly</li>
        <li class="star">some doc updates</li>
        <li class="star"><tt>GNUmakefile</tt>: "<tt>all</tt>" target does not include "<tt>doc</tt>" anymore, because BSDs (standard installation) often ship without <i>php</i>, <i>w3m</i> and <i>perl</i>.</li>
        <li class="plus">added the '<tt>[</tt>' and '<tt>]</tt>' keys to <i>ttyreplay</i> (lower/raise speed by 0.10)</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 2.11 (January 06 2005)&nbsp;&gt;</td>
    <td valign="top">

      <p class="block">Added support back in for the latest releases of the
      previous generation of kernels. For Linux, that's 2.4.something
      (USE DISCOURAGED!), and for FreeBSD that's 5.3.</p>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 2.10 (December 04 2005)&nbsp;&gt;</td>
    <td valign="top">

      <p class="block">Support for OpenBSD 3.8 and FreeBSD 6.0 was added;
      permission denied, syslog spamming, cpu hog, ownership and symlink
      recursion bugs were fixed. Docs were updated to reflect advances since
      the last seven releases.</p>

      <ul class="clog">
        <li class="plus">Support for OpenBSD 3.8 and FreeBSD 6.0</li>
        <li class="star">Documentation has been updated to reflect advances since the last seven releases.</li>
        <li class="star"><tt>user/replay.c</tt>: fixed a color bug when <tt>-t</tt> was active (spotted whilst under FreeBSD)</li>
        <li class="star"><tt>user/*.c</tt>: use <tt>usleep(10000)</tt> instead of <tt>sched_yield()</tt> to reduce idle cpu hog</li>
        <li class="star"><tt>user/rpld.c</tt>: umask was set wrong, so 1.) logging did not take place 2.) syslog was spammed with permission denied warnings</li>
        <li class="plus"><tt>user/rpld.c</tt>: added a rate limiter for syslog messages</li>
        <li class="star"><tt>user/rpld.c</tt>: find_devnode_dive() forgot to check for symlinks so that we dived into <tt>/dev/fd</tt>, which is not wanted, because <tt>/dev/fd/*</tt> itself includes <tt>/dev</tt> again, which lead to unwanted results.</li>
        <li class="star"><tt>user/rpld.c</tt>: fixed a filedescriptor leak in <tt>fill_info()</tt></li>
        <li class="plus"><tt>user/rpld.c</tt>: another try at getting ownerships right; rpld still had a case where the recorded owner differed from the owner of the <tt>/dev</tt> node (with respect to the design decision). See one below:</li>
        <li class="star"><tt>linux-2.6/rpldev.c</tt>: <tt>/dev/tty</tt> is a special case with respect to ownerships, so it must be filtered out in <tt>krpl_init()</tt> and <tt>krpl_open()</tt>.</li>
        <li class="star">using distribution-closer macros in specfile (e.g. <tt>%_bindir</tt> instead of <tt>/usr/bin</tt>)</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 2.06 (November 03 2005)&nbsp;&gt;</td>
    <td valign="top">

      <p class="block">This release was not publicly announced and rather
      served as a testbed, because new bugs were expected.</p>

      <ul class="clog">
        <li class="star">update to libHX 1.7.x</li>
        <li class="plus">rpld: added <tt>EVT_ID_STAMP</tt> containing the user and date the log was recorded</li>
        <li class="star">fixed: ttyreplay: <tt>argv[0]</tt>&nbsp;-- the program name, was also tried being replayed</li>
        <li class="star">ttyreplay: pctrl was not processed while waiting for more input data</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 2.03.5 (October 08 2005)&nbsp;&gt;</td>
    <td valign="top">

      <ul class="clog">
        <li class="star">update to libHX 1.6.2+</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 2.03.4 (September 22 2005)&nbsp;&gt;</td>
    <td valign="top">

      <ul class="clog">
        <li class="star"><tt>check_gcc</tt> macro gave a warning under *BSD because "<tt>@gcc</tt>" could not be found&nbsp;-- strange that it did not under Linux. Fixed anyway by reordering.</li>
        <li class="star">upstream upgrade: libHX 1.6.1</li>
        <li class="star"><tt>linux-2.6/rpldev.c</tt>: proper error propagation to userspace in <tt>mv_to_user()</tt></li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 2.03 (July 27 2005)&nbsp;&gt;</td>
    <td valign="top">

      <ul class="clog">
        <li class="plus">updated locales</li>
        <li class="star">new postal FSF address</li>
        <li class="plus">Support for: OpenBSD 3.7, FreeBSD 5.4, FreeBSD 6.0-beta1, (PCBSD 0.7.8)</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 2.02 (July 07 2005)&nbsp;&gt;</td>
    <td valign="top">

      <ul class="clog">
        <li class="star">update to libHX 1.5.3</li>
        <li class="plus">the correct locale name for the Persian translation is fa_IR, not ar_IQ</li>
        <li class="star">compilation: simpler dependency generation model</li>
        <li class="minus">ripped <i>popt</i> out, and replaced it by <i>HX_getopt</i></li>
        <li class="plus">support for OpenBSD 3.7</li>
        <li class="star">fixed: <tt>printf("It won't be possible ...")</tt> was given an uninitialized string</li>
        <li class="star">corrected: OpenBSD: ptc data was only discarded on i386</li>
        <li class="star">corrected: OpenBSD: tty data was enqueued into the ring buffer even if <tt>/dev/rpl</tt> was not open</li>
        <li class="plus">patch from Nguyen Anh Quynh to have the Makefiles find out whether <tt>-funit-at-a-time</tt> is supported</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 2.01 (released 2005-05-31)&nbsp;&gt;</td>
    <td valign="top">

      <ul class="clog">
        <li class="star">update to libHX 1.5.0 (<tt>so.0.5</tt>)</li>
        <li class="star">some translation updates (by far not complete)</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 2.00 (released 2005-03-29)&nbsp;&gt;</td>
    <td valign="top">

      <p><b>kpatch:</b></p>
      <ul class="clog">
        <li class="plus">support for Linux 2.4.29, 2.4.30-rc3</li>
        <li class="plus">support for 2.6.11, 2.6.11.*, 2.6.12-rc1 and 2.6.11.4-SUSE_9.3</li>
        <li class="plus">support for OpenBSD 3.6</li>
      </ul>

      <p><b>rpld:</b></p>
      <ul class="clog">
        <li class="plus">support for OpenBSD 3.6</li>
        <li class="plus">support for dynamic major/minor device numbers of the ttys</li>
        <li class="plus">supports "<tt>-</tt>" as an alias for <tt>/dev/stdin</tt> for <tt>-D</tt> option</li>
        <li class="plus">device path is now recorded into logfiles</li>
        <li class="star">ttys will now also show up in <i>rplctl</i> if they have just been touched, i.e. <tt>open()</tt>ed. There was a misconception where a <tt>/dev/pts/</tt> entry existed, but were not listed&nbsp;-- it was not a bug, but kwrite (kde "talk") that just opened it without using it.</li>
        <li class="star"><tt>-Q</tt> now sets all tty channels to <tt>IFP_DEACTIVATE</tt>, which means that they can be activated on a per-tty basis. (Before, the logfile always was <tt>/dev/null</tt> which did not leave much room for activation.)</li>
        <li class="star">byte counters continue to count even if tty is in <tt>IFP_DEACTIVE</tt> or <tt>IFP_DEACTIVSES</tt></li>
        <li class="star">infod: now uses '<tt>A</tt>', '<tt>D</tt>' and '<tt>S</tt>' for both text and parseable formats; format number bumped to 3</li>
        <li class="star">fixed: <tt>include/dev.h</tt> did not handle *BSD device numbering</li>
      </ul>

      <p><b>rplctl:</b></p>
      <ul class="clog">
        <li class="plus">fixed a "Broken pipe" which only showed up on *BSD&nbsp;-- it was not critical, but now the cosmetics fit again</li>
      </ul>

      <p><b>rpldev:</b></p>
      <ul class="clog">
        <li class="plus">timestamping is now done within kernelspace, it is more efficient there than it is in userspace</li>
        <li class="plus">added ioctls <tt>RPL_IOC_IDENTIFY</tt>, <tt>RPL_IOC_SEEK</tt>, <tt>RPL_IOC_FLUSH</tt> (because *BSD lack seek handling on char devs), so it must be synthesized</li>
      </ul>

      <ul class="clog">
        <li class="star">[Linux 2.4] fixed: could not compile because of <tt>tty-&gt;driver-&gt;major</tt> in <tt>PTY_MASTER</tt>; <tt>tty-&gt;driver.major</tt> is correct</li>
        <li class="plus">[Linux] send filename on <tt>kio_init()</tt>/<tt>kio_open()</tt></li>
      </ul>

      <ul class="clog">
        <li class="star">[FreeBSD] fixed: device could not be reopened when malloc failed</li>
        <li class="plus">[FreeBSD] implemented <tt>uio_ioctl()</tt></li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 1.44.2 (released 2005-02-10)&nbsp;&gt;</td>
    <td valign="top">

      <ul class="clog">
        <li class="star">update to libHX-0_3 (needs at least 20050206)</li>
        <li class="star">fixed: <tt>kpatch/freebsd-5.3.diff</tt> patched <tt>sys/conf.h</tt> in a way that could have possibly led to a memory corruption within the kernel</li>
        <li class="star">fixed: <tt>LDFLAGS</tt> was missing <tt>-pg</tt> in profiling mode</li>
        <li class="star">fixed: <tt>CFLAGS</tt> was missing <tt>-f<b>no</b>-omit-frame-pointer</tt> in profiling mode, being unable to compile (gcc exits with an error-warning)</li>
        <li class="plus">works with Linux 2.4.28 (vanilla) and 2.6.10 (vanilla)</li>
        <li class="plus">added <tt>ROOT</tt> and <tt>PREFIX</tt> makefile vars</li>
        <li class="plus">providing RPM packages for the userspace parts now. (kmod sources in <tt>/usr/src/ttyrpld-x.yy-kd/</tt>)</li>
        <li class="plus">added <tt>/usr/sbin/rcrpld</tt> as a symlink to <tt>/etc/init.d/rpld</tt> (rpm only)</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 1.44 (released 2005-01-20)&nbsp;&gt;</td>
    <td valign="top">

      <p><b>linux-2.*/rpldev.c:</b></p>
      <ul class="clog">
        <li class="star">fixed a one-off bug, where <tt>lseek()</tt> allowed to
          jump to e.g. <tt>0x10000</tt> (when <tt>Bufsize</tt> is 64K)&nbsp;--
          that's invalid, <tt>0x0ffff</tt> is the last valid position. this had
          caused <i>rpld</i> to exit without an apparent reason.</p>
      </ul>

      <p>FreeBSD is not affected.</p>

      <p><b>other:</b></p>
      <ul class="clog">
        <li class="star">minor changes to the docs</li>
        <li class="minus"><tt>--chkconf</tt> option removed, md5 checking does
          not work like it was thought:</li>
        <li class="star">if <tt>/etc/rpld.conf</tt> already exists, it is not
          overwritten</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 1.42 (released 2005-01-11)&nbsp;&gt;</td>
    <td valign="top">

      <p class="block">The long-awaited translations are in! Includes: ar_IQ
      (Persian-Farsi) [sic; fa_IR], de, es, fr, it, nb, nl and sv.</p>

      <p><b>linux-2.*/rpldev.c:</b></p>
      <ul class="clog">
        <li class="star">changed <tt>Open_lock</tt> scheme so that no mutex is held upon return to userspace</li>
        <li class="star"><tt>lseek()</tt> on the device will now change atime&nbsp;-- because we really slurp data rather than skipping it; it is lost after skip</li>
      </ul>

      <p><b>freebsd-5.3/rpldev.c:</b></p>
      <ul class="clog">
        <li class="plus"><tt>kio_ioctl()</tt> has been implemented</li>
      </ul>

      <p><b>misc:</b></p>
      <ul class="clog">
        <li class="star">fixed <tt>USER=</tt> in <tt>rpld.conf</tt> which became "<tt>root</tt>" by accident, also, <tt>START_INFOD=</tt> is now "<tt>yes</tt>" again</li>
        <li class="plus">updated docs with more BSD-specific stuff</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 1.40 (released 2004-12-24)&nbsp;&gt;</td>
    <td valign="top">

      <ul class="clog">
        <li class="plus">added support for FreeBSD 5.3</li>
        <li class="plus">added support for I18N</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 1.31 (released 2004-12-20)&nbsp;&gt;</td>
    <td valign="top">

      <p><b>rpld:</b></p>
      <ul class="clog">
        <li class="star">fixed crash when <tt>/sys/module/rpldev/Minor_nr</tt> does not exist and <tt>/proc/misc</tt> is being tried to read</li>
      </ul>

      <p><b>rpldev:</b></p>
      <ul class="clog">
        <li class="minus"><tt>kio_{init,open,read,write,ioctl}()</tt> now prepare data even more; they skip pty master data and transform the weird logic (as with <tt>EVT_DEINIT</tt>) to normal. This might reduce the CPU usage of <i>rpld</i>.</li>
        <li class="star"><tt>kio_{read,write}()</tt>: do not write zero-length data into ring buffer</li>
        <li class="star">fixed a <tt>NULL</tt> pointer dereference Oops which occurred when <tt>misc_register()</tt> failed</li>
        <li class="minus">fixed kernel memory corruption in <tt>mv_buffer2()</tt> and <tt>mv_to_user()</tt> when <tt>count==x</tt></li>
        <li class="minus">added missing <tt>Buffer_lock</tt> locks in <tt>uio_{open,close}()</tt></li>
        <li class="minus"><tt>struct {rpld,mem}_packet.dev2</tt> member has been obsoleted again</li>
      </ul>

      <p><b>other:</b></p>
      <ul class="clog">
        <li class="star"><tt>etc-init.d-rpldev</tt>: fixed typo</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 1.26 (released 2004-11-23)&nbsp;&gt;</td>
    <td valign="top">

      <p><b>rpld:</b></p>
      <ul class="clog">
        <li class="minus">pty master sides were also counted, which lead to correct but weird big numbers in the totale "BYTES IN" field in <i>rplctl</i></li>
      </ul>

      <p><b>infod:</b></p>
      <ul class="clog">
        <li class="star">fixed: doing `<tt>rplctl -L</tt>` on a tty which did not have a logfile associated crashed <i>rpld</i> (this was due to <tt>basename_pp()</tt>, introduced in 1.24)</li>
        <li class="star">fixed: not all stats were zeroed</li>
        <li class="plus">added per-tty zeroing</li>
      </ul>

      <p><b>rplctl:</b></p>
      <ul class="clog">
        <li class="star">fixed: <tt>COMPAT_MAJOR</tt> and <tt>COMPAT_MINOR</tt> macros were broken</li>
        <li class="plus"><tt>-L</tt> and <tt>-Z</tt> can now optionally accept a single tty</li>
        <li class="minus">removed <tt>-F</tt> option (<i>infod</i> code and doc part was removed long ago)</li>
      </ul>

      <p><b>rpldev:</b></p>
      <ul class="clog">
        <li class="plus">now compiles with GCC 3.4</li>
        <li class="minus">removed overrun warning</li>
      </ul>

      <p><b>other:</b></p>
      <ul class="clog">
        <li class="star">fixed: <tt>etc-init.d-rpld</tt> was copied to <tt>/etc/init.d/etc-init.d-rpld</tt> rather than <tt>/etc/init.d/rpld</tt></li>
        <li class="plus">`<tt>make install</tt>` will not overwrite <tt>/etc/rpld.conf</tt> if it is modified</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 1.24 (released 2004-11-18)&nbsp;&gt;</td>
    <td valign="top">

      <p><b>rpld:</b></p>
      <ul class="clog">
        <li class="star">internal: device number fixup has been moved to rpldev</li>
        <li class="plus">support for <tt>/sys/module/rpldev</tt> has been added</li>
        <li class="plus">added compat-style <tt>dev_t</tt> support (<tt>COMPAT_*</tt> in <tt>include/dev.h</tt>)</li>
        <li class="plus">better device number differentiation for different kernel versions (i.e. only minor 128 for 2.6, while 128...143 for 2.4 (for ptms))</li>
        <li class="plus"><tt>unlink()</tt> before <tt>mknod()</tt></li>
      </ul>

      <p><b>rpldev:</b></p>
      <ul class="clog">
        <li class="star">its <tt>Makefile</tt> has been changed to also take a variable <tt>KERNEL_DIR</tt> and <tt>MODULES_DIR</tt></li>
        <li class="plus">using new <tt>module_param()</tt> for 2.6 environment</li>
        <li class="plus">added check for endianesses which are neither little nor big</li>
        <li class="plus">added the <tt>WOverrun</tt> flag controlling warning messages about buffer overrun</li>
      </ul>

      <p><b>kpatch:</b></p>
      <ul class="clog">
        <li class="plus">added <tt>kpatch/2.0.40.diff</tt> and <tt>kpatch/2.2.26.diff</tt>, maybe it is not useful but it is there for completeness</li>
      </ul>

      <p><b>doc:</b><br />
      Countless minor (sometimes major) adjustments.</p>
      <ul class="clog">
        <li class="plus">desc: Staying in sync with the short desc and trove cat'gz. set on SF</li>
        <li class="plus">desc: Listing the four core components</li>
        <li class="plus">install: building notes for 2.4 and before</li>
        <li class="plus">rpldev(4): mentioning <tt>/sys</tt> things</li>
        <li class="star">support: largely rewritten to include the new things: FRS/SF, Patches and Feature Request Tracker, Mailing Lists</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 1.21 (released 2004-11-13)&nbsp;&gt;</td>
    <td valign="top">

      <p><b>rpld:</b></p>
      <ul class="clog">
        <li class="star">corrected condition inversion in <tt>G_skip()</tt></li>
        <li class="plus">redesigned the locking scheme (which is needed between <i>rpld</i>&lt;-&gt;<i>infod</i>) down to one big lock (which is also taken less in certain places).</li>
        <li class="star"><tt>log_write()</tt> now uses a one-shot read</li>
        <li class="plus">will now automatically create parent directories if needed (like `<tt>mkdir -p</tt>`)</li>
        <li class="plus">sets umask so that the maximum permissions are <tt>-wx------</tt></li>
        <li class="plus">added <tt>-i</tt> option (counter <tt>-I</tt>)</li>
      </ul>

      <p><b>infod:</b></p>
      <ul class="clog">
        <li class="minus"><tt>FLUSH</tt> request has been dropped, entries are automatically flushed by <i>rpld</i></li>
        <li class="minus">display of file descriptor has been removed</li>
      </ul>

      <p><b>rplctl:</b></p>
      <ul class="clog">
        <li class="star">option change: <tt>-D</tt>&nbsp;=&gt; <tt>-S</tt> (close until session ends / tty is deinitialized), <tt>-J</tt>&nbsp;=&gt; <tt>-D</tt> (deactivate total)</li>
      </ul>

      <p><b>rpldev:</b></p>
      <ul class="clog">
        <li class="star">(big-endian only) fixed <tt>my_swab()</tt> [had <tt>count=={16,32,64}</tt> instead of <tt>{2,4,8}</tt>]</li>
      </ul>

      <p><b>ttyreplay:</b></p>
      <ul class="clog">
        <li class="minus">outsourced the <tt>EVT_WRITE</tt> branch from <tt>replay_file()</tt> into <tt>e_proc()</tt></li>
        <li class="plus">added the echo key to also show <tt>EVT_READ</tt> packets</li>
        <li class="star">fixed a possible loss of precision in the "<tt>i-&gt;factor != 1.0</tt>" branch (added <tt>(double)</tt> explicit conversion)</li>
        <li class="star">replaced "packet skipping" with <tt>read_through()</tt> because otherwise screen initializations might be missing</li>
      </ul>

      <p><b>doc:</b></p>
      <ul class="clog">
        <li class="star">rpl(4) moved to rpl(5)&nbsp;-- now it's in the appropriate category</li>
        <li class="star">fixed some spellings, constructs and etc.</li>
        <li class="minus">took out some old paragraphs and</li>
        <li class="minus">removed <tt>doc/design.txt</tt>, it was already in ttyrpld(7)</li>
        <li class="plus">updated docs with new stuff, check it out!</li>
        <li class="plus">new design</li>
      </ul>

      <p><b>Makefile:</b></p>
      <ul class="clog">
        <li class="minus"><tt>MARCH</tt> and <tt>MCPU</tt> variables have been taken out, superseded by <tt>EXT_CFLAGS</tt></li>
        <li class="star">"<tt>kmod</tt>" target will now `<tt>rmmod rpldev</tt>` before reloading it</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 1.13.1 (released 2004-11-03)&nbsp;&gt;</td>
    <td valign="top">

      <ul class="clog">
        <li class="minus">Removed stray <tt>.orig</tt> file in <tt>kpatch/</tt></li>
        <li class="plus">added <tt>kpatch/2.6.9-vanilla</tt></li>
        <li class="star">added a workaround for GCC &gt;= 3.4 which has problems with inline</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 1.13 (released 2004-11-02)&nbsp;&gt;</td>
    <td valign="top">

      <p class="block">Ttys still were not closed for some reasons, so that
      once opened ttys dev numbers would never be logged again, or at least
      &lt;...&gt; anyway something was wrong. And it was due some bad code in
      UML... I relied on, sigh.</p>

      <p class="block">Anyway, it's corrected now. And it was painstaking to
      find the right spots myself, because actually UML placed a CLOSE where an
      OPEN should have been. OTOH, UML already had a OPEN, so I had two of
      them. Which lead me to the conclusion to redesign it a little bit.</p>

      <p><b>rpld/rpldev:</b></p>
      <ul class="clog">
        <li class="star">changed the <tt>EV_</tt> constants to <tt>EVT_</tt> to avoid clashes with <tt>ELF</tt> code</li>
        <li class="plus">new <tt>EVT_{INIT,OPEN,CLOSE,DEINIT}</tt> event types</li>
        <li class="star">the event type and magic bytes have not been touched since the on-disk format (as generated by rpld) remains the same, it's just a change between the "protocol" used between <i>rpldev</i> and <i>rpld</i>.</li>
        <li class="plus"><tt>.dev2</tt> member has been added to the <tt>struct rpld_packet</tt></li>
        <li class="star"><tt>struct {rpld,mem,disk}_packet</tt> are now overall tagged with PACKED</li>
      </ul>

      <p><b>rpldev:</b></p>
      <ul class="clog">
        <li class="plus">new <tt>krn_{init,deinit}()</tt></li>
        <li class="plus">tagged certain vars as <tt>__user</tt></li>
        <li class="plus">properly including <tt>&lt;linux/compiler.h&gt;</tt> or defining <tt>__user</tt> to nothing</li>
        <li class="minus">removed unnecessary byteswap operations (only relevant for big-endian)</li>
      </ul>

      <p><b>kpatch:</b></p>
      <ul class="clog">
        <li class="plus">new patch against 2.6.9-rc2</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 1.01 (never actually released)&nbsp;&gt;</td>
    <td valign="top">

      <p><b>infod:</b></p>
      <ul class="clog">
        <li class="star">changed the protocol to allow requests with "void", "int" and "string" parameters</li>
      </ul>

      <p><b>rplctl:</b></p>
      <ul class="clog">
        <li class="star">option parser behavior changed: <tt>-tL</tt> (<tt>-t -L</tt>) is now different from <tt>-Lt</tt> (<tt>-L -t</tt>) in that the order is significant... in <tt>-Lt</tt>, <tt>-L</tt> is executed before <tt>-t</tt> so it would still show readable text rather than parseable.</li>
      </ul>

      <p><b>other:</b></p>
      <ul class="clog">
        <li class="plus">added one more block to the legal statement</li>
        <li class="plus">added <tt>ETCINITD_QUIET</tt> to <tt>rpld.conf</tt></li>
        <li class="star">corrected the make <tt>clean</tt> target for "<tt>kernel-2.6</tt>" directory so that it continues to work when something changes in the kernel tree</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 1.00 (released 2004-09-26)&nbsp;&gt;</td>
    <td valign="top">

      <p class="block">New edict: if I don't explicitly say "you need to/should
      upgrade the rpldev kernel module" under "rpldev:" in this Changelog, then
      you don't need to! (Though, you always can. It's just a module and a
      cheap recompile without reboot.)</p>

      <p><b>rpld:</b></p>
      <ul class="clog">
        <li class="plus">will continue starting <i>rpld</i> even if config file could not be read</li>
        <li class="plus">warning message if the RPL device returns <tt>EBUSY</tt></li>
        <li class="star">fixed: <tt>/var/run/.rpldev</tt> was not unlinked if it could not be opened</li>
      </ul>

      <p><b>infod:</b></p>
      <ul class="clog">
        <li class="star">the parsetext (`<tt>rplctl -t</tt>`) "status field" has been changed: from (A, D, S) to (A, J, D) to reflect the rplctl switches</li>
        <li class="plus">will now print a '<tt>?</tt>' as status character if something got us corrupted</li>
      </ul>

      <p><b>ttyreplay:</b></p>
      <ul class="clog">
        <li class="plus">will now assume to play STDIN if no filenames are given and STDIN really is a pipe/file/etc. (anything but terminal)</li>
        <li class="plus">jumping to a given position (<tt>-J and -j</tt>) will now print the packets' contents to properly display e.g. graphical applications</li>
        <li class="plus">added play controls (forward 10/60 sec, pause, slower/faster playback, prev/next file, see DOCS!)</li>
        <li class="plus">displays the timestamp during replay (<tt>-t</tt> option)</li>
      </ul>

      <p><b>rpldev:</b></p>
      <ul class="clog">
        <li class="star">worked around: the device driver could not be opened with 2.6.8 due to 2.6.8's new seeking design</li>
        <li class="minus">removed the debugging messages "RPL device opened" and .."closed"</li>
      </ul>

      <p><b>etc-init.d-ttyrpld:</b></p>
      <ul class="clog">
        <li class="star">now uses the "install paths", i.e. <tt>/usr/local/sbin/</tt> rather than <tt>/usr/lib/ttyrpld</tt></li>
        <li class="plus">can work independent of <tt>/etc/rc.status</tt> (sometimes only to be found on SUSE Linux)</li>
      </ul>

      <p><b>other:</b></p>
      <ul class="clog">
        <li class="plus">added "<tt>install</tt>" and "<tt>uninstall</tt>" targets to Makefile</li>
        <li class="star"><tt>START_INFOD</tt> in the default distribution is now "<tt>yes</tt>"</li>
        <li class="star">(all): big endian was never selected due to a typo in <tt>__BYTE_ORER</tt></li>
        <li class="plus">supporting Kernel 2.6.8-14 (SUSE) and 2.6.8 (vanilla)</li>
        <li class="star">Kernel patches moved into their own folder "<tt>kpatch</tt>"</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 0.88 (released 2004-09-09)&nbsp;&gt;</td>
    <td valign="top">

      <p><b>rpld:</b></p>
      <ul class="clog">
        <li class="star">fixed a race condition in <tt>log_close()</tt>, where everything above <tt>pthread_mutex_lock()</tt> should have been while the lock is held</li>
        <li class="plus">user changes on tty device (such as on vc-*) will be detected and now correctly put into separate files. (See <tt>doc/rpld.html</tt> for details.)</li>
        <li class="star">fixed: a REMOVE request (`<tt>rplctl -X</tt>`) also triggered FLUSH (<tt>-F</tt>)</li>
      </ul>

      <p><b>infod:</b></p>
      <ul class="clog">
        <li class="star">changed <tt>IFP_GETINFO</tt> into <tt>IFP_GETINFO_T</tt> (readable text output) and</li>
        <li class="plus">added <tt>IFP_GETINFO</tt> (parseable text output, e.g. <tt>sscanf()</tt>)</li>
      </ul>

      <p><b>rplctl:</b></p>
      <ul class="clog">
        <li class="plus">added the <tt>-L</tt> switch to make <i>rplctl</i> ask a <tt>IFP_GETINFO/_T</tt> even if there are other arguments like <tt>-ADFJX</tt></li>
        <li class="plus">added the <tt>-t</tt> switch to use <tt>IFP_GETINFO</tt> instead of <tt>IFP_GETINFO_T</tt></li>
        <li class="star">fixed: `<tt>rplctl -X tty1 tty2</tt>` actually did not show info for tty2 (normal arguments ignored if any option switches were found)</li>
        <li class="plus">added the <tt>-Z</tt> switch to zero all stats counters</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 0.87 (released 2004-09-06)&nbsp;&gt;</td>
    <td valign="top">

      <p><b>rpld:</b></p>
      <ul class="clog">
        <li class="star"><tt>notify()</tt> will now print to stderr and syslog if both of <tt>-s</tt> and <tt>-v</tt> are given (before, <tt>-v</tt> overrode <tt>-s</tt>)</li>
        <li class="star">fixed: <tt>/proc/misc</tt> was not closed</li>
        <li class="star">temporary device node path is now <tt>/var/run/.rpldev</tt> (was <tt>./.rpldev</tt>)</li>
      </ul>

      <ul class="clog">
        <li class="plus">added a subcomponent infod (socket is /var/run/.rplinfod_socket)
        <li class="plus">added the respective gather tool "rplctl"
      </ul>

      <ul class="clog">
        <li class="star">fixed: ttys were not removed from the tree in log_close()
      </ul>

      <p><b>rpldev:</b></p>
      <ul class="clog">
        <li class="plus">added <tt>RPL_IOC_GET[RW]AVAIL</tt> ioctl calls</li>
        <li class="star">fixed: a deadlock would occur if <tt>kmalloc()</tt> returned <tt>NULL</tt> and you tried to reload the module</li>
      </ul>

      <ul class="clog">
        <li class="plus">using <tt>vmalloc()</tt> to be able to allocate more than 64KB (<tt>GI_bufsize</tt>)</li>
      </ul>

      <p><b>ttyreplay:</b></p>
      <ul class="clog">
        <li class="plus">added the <tt>-m</tt> option to specify a maximum delay</li>
        <li class="plus">added the <tt>-j</tt> (skip packet count) and <tt>-J</tt> (skip time) options to start playing at a given position in the logfile</li>
      </ul>

      <p><b>etc-init.d-ttyrpld:</b></p>
      <ul class="clog">
        <li class="star"><i>rpld</i> will now be detached from the current session (but keep the current stdout/err to report errors).</li>
      </ul>

      <p><b>other:</b></p>
      <ul class="clog">
        <li class="star">stepped once more through in theory and checked for necessary <tt>swab()</tt>s, both <i>rpldev</i> (added a lot) and <i>rpld</i> (more thought)</li>
        <li class="plus">logging on-the-fly-over-network has been tested and works well the two minutes I tested it</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 0.85 (released 2004-08-27)&nbsp;&gt;</td>
    <td valign="top">

      <ul class="clog">
        <li class="star">trying to make the different situations work (read/seek on file/pipe/dev)</li>
      </ul>

      <p><b>rpld:</b></p>
      <ul class="clog">
        <li class="star">the on-disk packet format has been changed in that the device field has been removed; <i>rpld</i> was designed to analyze this field and write the buffer to a given file, which makes the <tt>.dev</tt> field a space waster.</li>
        <li class="plus">see above: now saving up to 10%</li>
      </ul>

      <ul class="clog">
        <li class="plus">will now generate an <tt>EV_IDENT</tt> packet for log files</li>
        <li class="plus">implemented the "text-only" output of logfiles (strip headers), option is <tt>-T</tt></li>
      </ul>

      <ul class="clog">
        <li class="minus">superfluous: removed the <tt>BSIZE</tt>, <tt>FBSIZE</tt> options. The device hands out its buffer size through an ioctl.</li>
        <li class="plus"><tt>include/rpl_packet.h</tt>: added an <tt>EV_IGNORE</tt> type</li>
      </ul>

      <ul class="clog">
        <li class="star">fixed: <tt>EV_CLOSE</tt> events only happen on pty master sides, so log files did not get closed for ptys</li>
        <li class="plus">added a debug option <tt>-Q</tt> to suppress creating files</li>
        <li class="plus">added a check to stop reading from non-rpldev when EOF is reached</li>
        <li class="star">some endianess fixes, untested though</li>
        <li class="star">fixed: exclusion of pty masters only worked for major 128</li>
      </ul>

      <ul class="clog">
        <li class="plus">BSD pty master fixup as it just went with Unix98 pty master (i.e. to catch the fact that the close is reported on the master side)</li>
      </ul>

      <p><b>ttyreplay:</b></p>
      <ul class="clog">
        <li class="minus"><tt>--std</tt> option has been removed, Overhead Correction is also needed on 2.6 when using high values with <tt>-S</tt></li>
      </ul>

      <p><b>rpldev kernel module:</b></p>
      <ul class="clog">
        <li class="plus"><tt>uif_llseek()</tt> will now not lock if it is not actually seeking</li>
      </ul>

      <p><b>other:</b></p>
      <ul class="clog">
        <li class="plus"><tt>etc-init.d-ttyrpld</tt>: added missing "kick" help to (*) case</li>
        <li class="plus"><tt>include/rpl_packet.h</tt>: new event type: <tt>EV_IDENT</tt></li>
        <li class="star"><tt>include/rpl_packet.h</tt>: <tt>struct log_packet</tt> has been renamed to <tt>struct disk_packet</tt> to remove ambiguity</li>
        <li class="plus">new doc outfit and manual pages</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 0.83 (released 2004-08-05)&nbsp;&gt;</td>
    <td valign="top">

      <p><b>rpld:</b></p>
      <ul class="clog">
        <li class="plus">automatically determines what minor number the rpl device is bound to and dynamically create a node</li>
      </ul>

      <p><b>ttyreplay:</b></p>
      <ul class="clog">
        <li class="plus">added follow modes (<tt>-F</tt>, <tt>-f</tt> options)<br />
          <tt>-F</tt>: live feed (directly see what a user is typing), à la `<tt>tail -f</tt>`<br />
          <tt>-f</tt>: play file as normal and switch into <tt>-F</tt> when EOF is reached</li>
        <li class="plus">added error recovery</li>
      </ul>

      <p><b>rpldev:</b></p>
      <ul class="clog">
        <li class="star">the default minor number is now <tt>MISC_DYNAMIC_MINOR</tt> (see automatic minor number detection below for <i>rpld</i>)</li>
        <li class="plus">added an info upon module loading telling us what minor number the misc subsystem finally gave <i>rpldev</i></li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b>To 0.82 (released 2004-08-03)&nbsp;&gt;</td>
    <td valign="top">

      <p><b>rpld:</b></p>
      <ul class="clog">
        <li class="star">a one-time variable was not freed (minimal impact)</li>
        <li class="plus">added <tt>-v</tt> option to interactively show statistics</li>
        <li class="plus">a new option "<tt>OFMT</tt>" has been added which combines (and thus obsoletes) the <tt>LOGDIR</tt> and <tt>SBTTY</tt> variables</li>
        <li class="plus">added "<tt>%t</tt>" (time in "<tt>%H%M%S</tt>" format) as a specifier for <tt>OFMT</tt></li>
        <li class="star">"<tt>%d</tt>" has been degraded to only provide the date ("<tt>%Y%M%d</tt>") rather than date-and-time</li>
      </ul>

      <p><b>ttyreplay:</b></p>
      <ul class="clog">
        <li class="plus">finally implemented the "time warp" function (e.g. replay the log twice as fast as the original user on typed), it's the <tt>-S</tt> option ("speed"). It takes a multiplication factor (<tt>2.0</tt> = double, <tt>0.5</tt> = half speed, etc.)</li>
        <li class="star">the time correction algorithm has been redone (the one before still had a time skew); the main calculation unit is now µsec</li>
      </ul>

      <p><b>rpldev:</b></p>
      <ul class="clog">
        <li class="plus">the user may now choose the minor number upon module loading</li>
      </ul>

      <p><b>other:</b></p>
      <ul class="clog">
        <li class="plus">more precise error control in <tt>etc-init.d-ttyrpld</tt></li>
        <li class="star"><tt>user/shared.c</tt>: added defintions for BSD pty slaves, changed <tt>pty-%</tt> into proper <tt>ptyp%%</tt></li>
        <li class="star"><tt>kernel-2.6/rpl-kernelpatch.diff</tt>: the file has been replaced with patch which works for 2.6.5 and up</li>
      </ul>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="section"><b> Initial Release 0.80 (released 2004-07-19)&nbsp;&gt;</td>
    <td valign="top">

      <p>Initial release</p>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
<?php include_once("_footer.php"); ?>
