<?php
//version 1.0
//The script provides summary for Xtremio Capacity %Usage % free 
//This script requires a config file which has the FQDN of XMS Server 
// config.ini
//<host FQDN>
// Config file variable and checking if file exists
$config = 'config.ini';
if ( !file_exists ( $config )) {
echo "Config file missing.. \n";
exit;
}
//Read the Config file and Display Capacity Summary 
$file = fopen("$config","r");
print "MGMT Server\tCluster Name\t\tPSNT\tRed Factor\tSW Version\tCapacity\tUsage\t%Free\n";
while ( !feof ($file) ) {
$line = fgets($file);
if (!trim($line)) continue;
call_xtremio(trim($line));
}
fclose($file);
//Create call_xtremio function 
//This will ignore https ssl certficates warning , you can look at options to enable it 
// Uses default xtremio user user:user
function call_xtremio($xstor)
{
$ch = curl_init();
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
curl_setopt($ch,CURLOPT_URL,"https://user:user@xstor/api/json/types/clusters/1");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,60);
$output = curl_exec($ch);
$out_ar = json_decode($output);
$host=explode(".",$xstor);
print $host[0],"\t";
print $out_ar->content->name,"\t";
print $out_ar->content->{'sys-psnt-serial-number'},"\t";
print $out_ar->content->{'data-reduction-ratio-text'},"\t";
print $out_ar->content->{'sys-sw-version'},"\t";
$phy_cap = round($out_ar->content-{'ud-ssd-space'}/(1024*1024*1024),3);
print $phy_cap,"\t\t";
$use_cap = round($out_ar->content-{'ud-ssd-spacein-use'}/(1024*1024*1024),3);
print $use_cap,"\t";
$free_cap = $phy_cap - $use_cap;
$free_per = round(($free_cap/$phy_cap)*100,2);
print $free_per,"\n";
curl_close($ch);
}
// End of function call_xtremio
?>




