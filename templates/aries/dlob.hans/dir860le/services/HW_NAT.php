<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/phyinf.php";
include "/htdocs/phplib/xnode.php";

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");

function startcmd($cmd)	{fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)	{fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}
function error($errno)	{startcmd("exit ".$errno); stopcmd( "exit ".$errno);}

function HWNATsetup($dis_hwnat,$dis_fastnat)
{
	startcmd('echo 2 > /proc/fast_nat');
	startcmd('sleep 1');
	
	if($dis_hwnat==1)
	{
		startcmd('echo Disable Chip Vendor Hardware NAT  ...	> /dev/console');
		startcmd('echo 0 > /proc/hw_nat');
		
		startcmd('echo Enable Alpha Software NAT as Default ...	> /dev/console');
		startcmd('echo 1 > /proc/sys/net/ipv4/netfilter/ip_conntrack_fastnat');
	}
	else
	{
		startcmd('echo Disable Alpha Software NAT  ...	> /dev/console');
		startcmd('echo 0 > /proc/sys/net/ipv4/netfilter/ip_conntrack_fastnat');
		
		startcmd('echo Enable Chip Vendor Hardware NAT  ...	> /dev/console');
		startcmd('echo 9 > /proc/hw_nat');
		startcmd('sleep 1');
		startcmd('echo 1 > /proc/hw_nat');
	}
	if($dis_fastnat==1)
	{
		startcmd('echo Disable Chip Vendor Fast NAT  ...	> /dev/console');
		startcmd('echo 0 > /proc/fast_nat');
	}
	else
	{
		startcmd('echo Enable Chip Vendor Fast NAT  ...	> /dev/console');
		startcmd('echo 1 > /proc/fast_nat');
	}
	startcmd('iptables -Z -t mangle');
	
	
	stopcmd('echo Disable Chip Vendor Hardware NAT  ...	> /dev/console');
	stopcmd('echo 0 > /proc/hw_nat');
	stopcmd('echo Enable Alpha Software NAT as Default ...	> /dev/console');
	stopcmd('echo 1 > /proc/sys/net/ipv4/netfilter/ip_conntrack_fastnat');
}

$layout = query("/runtime/device/layout");
if ($layout=="router")
{
	$dis_hwnat_flag = 0;
	$dis_fastnat_flag = 0;
	$wan1_active = 0;
	$wan2_active = 0;

	/* check URL filter */
	if(query("/acl/accessctrl/enable")=="1")
	{
		foreach ("/acl/accessctrl/entry")
		{
			if( query("enable") == "1" &&
				query("webfilter/enable") == "1")
			{
				foreach ("/acl/accessctrl/webfilter/entry")
				{
					$url = query("url");
					if ($url != "")
					{
						$dis_hwnat_flag = 1;
						$found = 1;
						break;
					}
				}
				if($found == 1) {break;}
			}
		}
	}

	$if1path = XNODE_getpathbytarget("", "inf", "uid", "WAN-1", 0);
	$if2path = XNODE_getpathbytarget("", "inf", "uid", "WAN-2", 0);
	if ($if1path != "") {$wan1_active = query($if1path."/active");}
	if ($if2path != "") {$wan2_active = query($if2path."/active");}
	if($wan1_active == "1")
	{
		$if1_inet	= query($if1path."/inet");
		$if1_inetp	= XNODE_getpathbytarget("/inet", "entry", "uid", $if1_inet, 0);
		$if1_addrtype = query($if1_inetp."/addrtype");
		$if1_over   = query($if1_inetp."/ppp4/over");
		
		if($if1_addrtype != "ipv4")
		{
			/* check PPTP */
			if($if1_over == "pptp"){$dis_hwnat_flag = 1;}
			/* check L2TP */
			if($if1_over == "l2tp"){$dis_hwnat_flag = 1;}
		}
	}
	
	if($wan2_active == "1")
	{
		$if2_inet	= query($if2path."/inet");
		$if2_inetp	= XNODE_getpathbytarget("/inet", "entry", "uid", $if2_inet, 0);
		$if2_addrtype = query($if2_inetp."/addrtype");
		$if2_over   = query($if2_inetp."/ppp4/over");
		
		if($if2_addrtype != "ipv4")
		{
			/* check PPTP */
			if($if2_over == "pptp"){$dis_hwnat_flag = 1;}
			/* check L2TP */
			if($if2_over == "l2tp"){$dis_hwnat_flag = 1;}
		}
	}
	
	/* check Multi-PPPoE */
	if($wan1_active == "1" && $wan2_active == "1")
	{
		if($if1_addrtype != "ipv4" && $if2_addrtype != "ipv4")
		{
			if($if1_over == "eth" && $if2_over == "eth"){$dis_hwnat_flag = 1;}
		}
	}
	
	/* Qos
	   We move the check to bwcsvcs.php.
	*/
	
	
	/* check IP Unnumbered */
	$ipu_cnt = query("/route/ipunnumbered/count");
	foreach ("/route/ipunnumbered/entry")
	{
		if ($InDeX > $ipu_cnt) break;

		$ipu_en	  = query("enable");
		$ipu_netid= query("network");
		$ipu_mask = query("mask");
	
		if($ipu_en=="1" && $ipu_netid!="" && $ipu_mask!="")
		{
			$dis_hwnat_flag = 1;
		}
	}

	/* check STATIC ROUTE */
	$stt_cnt = query("/route/static/count");
	foreach ("/route/static/entry")
	{
		if ($InDeX > $stt_cnt) break;
	
		$stt_en	  = query("enable");
		$stt_netid= query("network");
		$stt_mask = query("mask");
		$stt_dev  = PHYINF_getruntimeifname(query("inf"));
	
		if ($stt_en=="1" && $stt_dev!="" && $stt_netid!="" && $stt_mask!="" )
		{
			$dis_hwnat_flag = 1;
		}
	}

	/* check DOMAIN ROUTE */
	foreach ("/runtime/inf")
	{
		$dm_addrtype = query("inet/addrtype");
		if (query("inet/".$dm_addrtype."/valid")!="1") continue;
		if		($dm_addrtype=="ipv4") $dm_gw = query("inet/ipv4/gateway");
		else if	($dm_addrtype=="ppp4") $dm_gw = query("inet/ppp4/peer");
		else continue;
		foreach ("dnscache/target")
		{
			$dis_hwnat_flag = 1;
			break;
		}
	}

	/* check DEST ROUTE */
	$dst_cnt = query("/route/destination/count");
	if ($dst_cnt=="") $dst_cnt=0;
	foreach ("/route/destination/entry")
	{
		/* entry count */
		if ($InDeX > $dst_cnt) break;
	
		/* Skip if disabled */
		if (query("enable")!="1") continue;
		/* The interface must be up to add the route. */
		$dst_infp = XNODE_getpathbytarget("/runtime", "inf", "uid", query("inf"), 0);
		if ($dst_infp == "") continue;
		/* Get the gateway, we use 'via' in the routing rule. */
		$dst_addrtype = query($dst_infp."/inet/addrtype");
		if (query($dst_infp."/inet/".$dst_addrtype."/valid")!="1") continue;
		if		($dst_addrtype == "ipv4") $dst_gateway = query($dst_infp."/inet/ipv4/gateway");
		else if	($dst_addrtype == "ppp4") $dst_gateway = query($dst_infp."/inet/ppp4/peer");
		else continue;
		$dis_hwnat_flag = 1;
	}

	/* check DYNAMIC ROUTE */
	$enable_rip = query("/route/dynamic/rip/enable");
	if($enable_rip=="1")
	{
		if(query("/route/dynamic/rip/count")!="0")
		{
			$dis_hwnat_flag = 1;
		}
	}
	
	/* +++ START Hardware NAT and Fast NAT +++ */
	if($wan1_active == "1" || $wan2_active == "1") {HWNATsetup($dis_hwnat_flag,$dis_fastnat_flag);}
}

error(0);
?>
