<? if($ROUTING_INDEX%2==0)
	echo '<tr class="light_bg">';
   else
	echo '<tr class="gray_bg">';
?>
	<td rowspan="2" class="border_left gray_border_btm">
		<input type=checkbox id="<? echo 'enable_'.$ROUTING_INDEX;?>">
		<input type="hidden" id="<?echo "uid_".$ROUTING_INDEX;?>" value="<?=$ROUTING_INDEX?>"/>	
	</td>
	<td class="border_left gray_border_btm">Name<br/><input type=text id="<? echo 'name_'.$ROUTING_INDEX;?>" size=16 maxlength=15><?drawlabel("<? echo 'name_'.$ROUTING_INDEX;?>");?></td>
	<td class="border_left gray_border_btm">Destination IP<br/><input type=text id="<? echo 'dstip_'.$ROUTING_INDEX;?>" size=16 maxlength=15><?drawlabel("<? echo 'dstip_'.$ROUTING_INDEX;?>");?></td></td>
	<td rowspan="2" class="border_left gray_border_btm">Metric<br/><input type=text id="<? echo 'metric_'.$ROUTING_INDEX;?>" size=3 maxlength=3><?drawlabel("<? echo 'metric_'.$ROUTING_INDEX;?>");?></td>
	
	<td rowspan="2" class="border_left gray_border_btm">Interface<br/>
		<select id="<?echo "inf_".$ROUTING_INDEX;?>" style="width: 150px;">
			
<?		
			include "/htdocs/phplib/xnode.php";
			
			/*
				D-link spec. 
				If PPTP, should show 
				 WAN (ip)
				 WAN Physical (ip)
				When in PPTP mode, we use the value of 'lowerlayer' decide which ifname is physical. 
			*/
			$wan_lowerlayer = "";
			
			$i=1;
			while ($i>0 && $i<4)
			{
				$ifname = "WAN-".$i;
				$ifpath = XNODE_getpathbytarget("runtime", "inf", "uid", $ifname, 0);
				$ifpath2 = XNODE_getpathbytarget("", "inf", "uid", $ifname, 0);
				
				if ($ifpath == "") { $i++; continue; }
				
				$inet_addrtype = query($ifpath."/inet/addrtype");
				
				$lowerlayer = query($ifpath2."/lowerlayer");
				if($lowerlayer != "") { $wan_lowerlayer = $lowerlayer; }
				
				$str = "";
				//if($wan_lowerlayer == $ifname) { $str = "Physical"; }
				//for normal PPTP, we skip the physical interface, since we don't do NAT in this physical interface.
				//if want to set routing to this interface, then we should do NAT in this physical.
				if($ifname == $wan_lowerlayer) { $i++; continue; }
				
				if($inet_addrtype == "ipv4")
				{
					$ip = query($ifpath."/inet/ipv4/ipaddr");
					$show_ifname = "WAN ".$str." (".$ip.")";
				}
				else if($inet_addrtype == "ppp4")
				{
					$ip = query($ifpath."/inet/ppp4/local");
					$show_ifname = "WAN ".$str. "(".$ip.")";
				}
				else
				{
					$i++; 
					continue;
				}
					echo '<option value="'.$ifname.'">'.$show_ifname.'</option>';
					$i++;
			}
			
			/*
			//$i=1;
			$i=3;
			while ($i>0 && $i<4)
			{
				$ifname = "LAN-".$i;
				$ifpath = XNODE_getpathbytarget("runtime", "inf", "uid", $ifname, 0);
				//if ($ifpath == "") { $i++; continue; }
				if ($ifpath == "") { $i--; continue; }
				$inet_uid = query($ifpath."/inet/uid");
				$inet_path = XNODE_getpathbytarget("inet", "entry", "uid", $inet_uid, 0);
				$inet_addrtype = query($inet_path."/addrtype");
				
				//$show_ifname = $ifname;
		        if($inet_addrtype == "ipv4")
		        {
					//$show_ifname = $ifname."(".query($inet_path.'/ipv4/ipaddr').")";
					$show_ifname = $ifname;
		        } else
		        {
		        	//$i++;
		        	$i--;
		        	continue;	
		        }
		        	
				echo '<option value="'.$ifname.'">'.$show_ifname.'</option>';
				//$i++;
				$i--;
			}
			*/
?>
		</select>
	</td>
</tr>
<? if($ROUTING_INDEX%2==0)
	echo '<tr class="light_bg">';
   else
	echo '<tr class="gray_bg">';
?>
	<td class="border_left gray_border_btm">Netmask<br/><input type=text id="<? echo 'netmask_'.$ROUTING_INDEX;?>" size=16 maxlength=15><?drawlabel("<? echo 'netmask_'.$ROUTING_INDEX;?>");?></td>
	<td class="border_left gray_border_btm">Gateway<br/><input type=text id="<? echo 'gateway_'.$ROUTING_INDEX;?>" size=16 maxlength=15><?drawlabel("<? echo 'gateway_'.$ROUTING_INDEX;?>");?></td>
</tr>

