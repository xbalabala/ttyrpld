<?php
  ob_start();
  $man = $_GET["man"] == "y" || $_SERVER["argv"][1] == "man";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="format-white.css" />
<link rel="alternate stylesheet" type="text/css" href="format-black.css" title="Black background" />
<title>ttyrpld - TTY replay daemon</title>
</head>

<body>

<p style="background-color: #000000; padding: 5px;"><a
href="http://ttyrpld.sourceforge.net/"><img src="ttyrpld.png"
alt="ttyrpld logo" border="0" /></a></p>

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
