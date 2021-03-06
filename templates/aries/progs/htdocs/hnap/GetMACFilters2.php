HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
$enabled="false";
$allow="false";
if(query("/acl/macctrl/policy")!="DISABLE")
{
	$enabled="true";
}
if(query("/acl/macctrl/policy")=="ACCEPT" || query("/acl/macctrl/allow")=="true")
{
	$allow="true";
}

//$allow = "";//Sammy, workaround, prevent QRS crash
?>

<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body><GetMACFilters2Response xmlns="http://purenetworks.com/HNAP1/"><GetMACFilters2Result>OK</GetMACFilters2Result><Enabled><?=$enabled?></Enabled><IsAllowList><?=$allow?></IsAllowList>
<MACList><?
	foreach("/acl/macctrl/entry")
	{
		echo "<MACInfo>";
		echo "<MacAddress>".query("mac")."</MacAddress>";
		echo "<DeviceName>".get("x","description")."</DeviceName>";
		echo "</MACInfo>";
	}
	?></MACList></GetMACFilters2Response></soap:Body></soap:Envelope>
