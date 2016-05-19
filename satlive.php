<?php
include('/home/satfg8ojco/www/script_connect.php');

$speedlight=299792458/1000;

$sat=strtoupper($_GET['s']);
if (strlen($sat)>0) setcookie("sat",$sat,time()+3600*24*30);
if (strlen($sat)==0) $sat=$_COOKIE["sat"];
if (strlen($sat)==0) $sat='SO-50';

$output=$_GET['o'];

$locator=strtoupper($_GET['l']);
if (strlen($locator)>0) setcookie("locator",$locator,time()+3600*24*30);
if (strlen($locator)==0) $locator=$_COOKIE["locator"];
if (strlen($locator)==0) $locator='AA00aa';

$elevation=strtoupper($_GET['e']);
if (strlen($elevation)>0) setcookie("elevation",$elevation,time()+3600*24*30);
if (strlen($elevation)==0) $elevation=$_COOKIE["elevation"];
if (strlen($elevation)==0) $elevation=0;

$altitude=strtoupper($_GET['a']);
if (strlen($altitude)>0) setcookie("altitude",$altitude,time()+3600*24*30);
if (strlen($altitude)==0) $altitude=$_COOKIE["altitude"];
if (strlen($altitude)==0) $altitude=0;

$ia=$_GET['ia'];
$aa=$_GET['aa'];
if (strlen($ia)>0) setcookie("ia",$ia,time()+3600*24*30);
if (strlen($ia)==0) $ia=$_COOKIE["ia"];
if (strlen($aa)>0) setcookie("aa",$aa,time()+3600*24*30);
if (strlen($aa)==0) $aa=$_COOKIE["aa"];
if (strlen($ia)==0) $ia=0;
if (strlen($aa)==0) $aa=360;

$it=$_GET['it'];
$at=$_GET['at'];
if (strlen($it)>0) setcookie("it",$it,time()+3600*24*30);
if (strlen($it)==0) $it=$_COOKIE["it"];
if (strlen($at)>0) setcookie("at",$at,time()+3600*24*30);
if (strlen($at)==0) $at=$_COOKIE["at"];
if (strlen($it)==0) $it=8;
if (strlen($at)==0) $at=22;
if ($at<=$it) $at=24;

$verif=$_GET['v'];
if (strlen($_GET['v'])>0) {
	setcookie("verif",$verif,time()+3600*24*30);
} else {
	if (strlen($_COOKIE["verif"])>0) $verif=$_COOKIE["verif"];
}
	

$utc='Etc/GMT';

$format = 'Y-m-d H:i:s';   
$format2 = 's';   

//echo $output.'/'.$sat;exit();

$sql='SELECT * FROM sat WHERE ident="'.$sat.'"';
$rs=mysql_query($sql);
$j=mysql_fetch_array($rs);
$sat_id=$j['id'];
$ident=$j['ident'];
$active=true;
$sql='SELECT * FROM sat_mode WHERE sat='.$sat_id .' ORDER BY id desc';
$rs=mysql_query($sql);
while ($j2=mysql_fetch_array($rs)) {
	$uplink=$j2['uplink'];
	$downlink=$j2['downlink'];
	
	echo $ident.';'.$j2['name'].';'.$j2['uplink_mode'].';'.$j2['downlink_mode'].';'.$j2['uplink'].';'.$j2['downlink'].';'.$j2['tone']."\n";
	
}

$eol = "\r\n";

echo "@@@@@@@\n";

date_default_timezone_set($utc);

require_once 'Predict.php';
require_once 'Predict/Sat.php';
require_once 'Predict/QTH.php';
require_once 'Predict/Time.php';
require_once 'Predict/TLE.php';

$predict  = new Predict();
$qth      = new Predict_QTH();
$qth->alt = $elevation; // Altitude in meters
$qth->lat = loc_to_latlon($locator)[0];
$qth->lon = loc_to_latlon($locator)[1];

$tleFile = explode("\r\n",$j['tle']); // Load up the ISS data file from NORAD

$tle     = new Predict_TLE($tleFile[0], $tleFile[1], $tleFile[2]); // Instantiate it
/*
$tle     = new Predict_TLE("SO-50
","1 27607U 02058C   15365.21816985  .00000296  00000-0  62542-4 0  9997","2 27607  64.5559 317.0579 0080821 250.8155 108.4188 14.75004350700348"); // Instantiate it
*/
$sattle     = new Predict_Sat($tle); // Load up the satellite data
$now     = Predict_Time::get_current_daynum(); // get the current time as Julian Date (daynum)

// You can modify some preferences in Predict(), the defaults are below
//
$predict->minEle     = $elevation; // Minimum elevation for a pass
$predict->timeRes    = 4; // Pass details: time resolution in seconds
$predict->numEntries = 2000000; // Pass details: number of entries per pass
$predict->threshold  = 360; // Twilight threshold (sun must be at this lat or lower)
//print_r($sattle);	
// Get the passes and filter visible only, takes about 4 seconds for 10 days
$results  = $predict->get_passes($sattle, $qth, $now,1);
//print_r($results);
$oldrange=18000000;
$dir=true;
for ($i=0;$i<count($results[0]->details);$i++) {
	$li=$results[0]->details[$i];
	$dt=Predict_Time::daynum2readable($li->time, $utc, $format);
	$dt2=Predict_Time::daynum2unix2($li->time);
	$az=round($li->az,0);
	$el=round($li->el,0);
	$range=round($li->range,3)*1000;
	if ($range<$oldrange) {
		$dir=true;
	} else {
		$dir=false;
	}
	$oldrange=$range;
	echo $dt2.';'.$az.';'.$el.';'.$range.';';
	
	$velo=($li->velo*1000);

	if ($dir==true) {
		echo '0;';
	} else {
		echo '1;';
		
	}
	/*
	$z=($li->range*1);
	$y=(6378+$elevation);
	$x=(6378+($li->alt));
//	echo '////'.$x.'/'.$y.'/'.$z.'/';
	$tmp=((pow($x,2) + pow($z,2) - pow($y,2))/ (2 * $x * $z));
//echo $tmp . '='.(acos($tmp)*180/pi()) ."\n";
//	echo acos((pow($x,2) + pow($z,2) - pow($y,2))/ (2 * $x * $z)) ."\n";
echo round(acos($tmp)*180/pi(),0) .";";
	$ib=acos((pow($x,2) + pow($z,2) - pow($y,2))/ (2 * $x * $z))*180/pi();
	$ib2=acos((pow($y,2) + pow($z,2) - pow($x,2))/ (2 * $y * $z))*180/pi();
echo $li->range .';'.$ib .'/'.$ib2 .';';
	
	*/
	
	/*
	$uv = $velo*cos($angleM*pi()/180);
	$uv2 = $velo*cos($angleD*pi()/180);
	$vv = $speedlight/($uv+$speedlight);
	$vv2 = $speedlight/($uv2+$speedlight);
	$updop = $vv*$uplink;
	$dwdop = $vv2*$downlink;
	
	*/
	$updop = $uplink*($li->range_rate / $speedlight);
	$dwdop = $downlink*($li->range_rate / $speedlight);	
	

	echo round($updop).';';
	echo round($dwdop).';';
	
	/*
	$nupdop=round($updop/1000,0);
	if ($nupdop<>$oupdop) {
		echo $nupdop.';';
		$oupdop=$nupdop;
	}
	$ndwdop=round($dwdop/1000,0);
	if ($ndwdop<>$odwdop) {
		echo $ndwdop;
		$odwdop=$ndwdop;
	}
	*/
	echo ";\n";
	
}
	
//print_r($results[0]);
	
	
		