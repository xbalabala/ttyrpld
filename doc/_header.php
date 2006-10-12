<?php
  ob_start();
  $man = $_GET["man"] == "y" || $_SERVER["argv"][1] == "man";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="format-white.css" />
<title>ttyrpld - TTY replay daemon</title>
</head>

<body>

<div style="background-color: #FFF6D5; border-bottom: 1px solid #808080;
padding: 5px;">
<table>
  <tr>
    <td valign="center"><a
        href="http://ttyrpld.sourceforge.net/"><img src="ttyrpld.png"
        alt="ttyrpld logo" border="0" /></a></td>
    <td valign="center">ttyrpld&nbsp;- tty logging daemon</td>
  </tr>
</table></div>

<p align="center">
<?php if(!$man) { ?>
  <a href="index.php">News</a> |
<?php } ?>
  <a href="desc.php">Description</a> |
  <a href="install.php">Install</a> |
<?php if($man) { ?>
  <a href="man_ttyreplay.1.php">ttyreplay(1)</a> |
  <a href="man_rpldev.4.php">rpldev(4)</a> |
  <a href="man_rpl.5.php">rpl(5)</a> |
  <a href="man_ttyrpld.7.php">ttyrpld(7)</a> |
  <a href="man_rplctl.8.php">rplctl(8)</a> |
  <a href="man_rpld.8.php">rpld(8)</a> |
  <a href="netlog.php">Netlogging</a> |
  <a href="changes.php">Changelog</a> |
<?php } ?>
<a href="support.php">Support</a></p>

<hr size="1" color="#404040" width="80%" />

<table style="margin-right: 15px;">
