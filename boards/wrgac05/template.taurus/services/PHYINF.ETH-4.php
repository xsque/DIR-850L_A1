<?
include "/etc/services/PHYINF/phyinf.php";
fwrite("w",$START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");
phyinf_setup("ETH-4");
fwrite("a", $START, "exit 0\n");
fwrite("a", $STOP,  "exit 0\n");
?>
