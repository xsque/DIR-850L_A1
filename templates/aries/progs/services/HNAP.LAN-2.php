<?
/* vi: set sw=4 ts=4: */
include "/etc/services/HTTP/httpsvcs.php";
fwrite("w",$START,"#!/bin/sh\n");
fwrite("w", $STOP,"#!/bin/sh\n");
httpsetup("LAN-2");
?>
