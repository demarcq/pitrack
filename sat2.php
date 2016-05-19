<?php

include('/home/satfg8ojco/www/script_connect.php');  

require_once 'Predict.php';
require_once 'Predict/Sat.php';
require_once 'Predict/QTH.php';
require_once 'Predict/Time.php';
require_once 'Predict/TLE.php';

$sat=$_GET['s'];
$t=$_GET['t'];
$sql='SELECT * FROM sat WHERE 1 ';
if (strlen($sat)>0) {
	$sql.=' AND ident="'.$sat.'" ';
} else {
	$sql.=' AND LENGTH(tle)>0';
	if ($t==0) 	$sql.=' AND etat=1 ';
}
$sql.=' ORDER BY ident ASC';
$old='';
$rs=mysql_query($sql);
while ($j=mysql_fetch_array($rs)) {
	$sat=$j['ident'];
	$obs_set      = new Predict_ObsSet();
	$sat_geodetic = new Predict_Geodetic();
	$obs_geodetic = new Predict_Geodetic();
	
	$predict  = new Predict();
	$qth      = new Predict_QTH();
	$qth->alt = $elevation; // Altitude in meters
	$qth->lat = loc_to_latlon($locator)[0];
	$qth->lon = loc_to_latlon($locator)[1];
//echo $j['tle'];	
	$tleFile = explode("\r\n",$j['tle']); // Load up the ISS data file from NORAD
	$tle     = new Predict_TLE($tleFile[0], $tleFile[1], $tleFile[2]); // Instantiate it
	
	$sattle     = new Predict_Sat($tle); // Load up the satellite data
	
	$sattle->jul_utc =Predict_Time::get_current_daynum(); // get the current time as Julian Date (daynum)
	$sattle->tsince = ($sattle->jul_utc - $sattle->jul_epoch) * 1.44E3;
	
	
	$sgpsdp = Predict_SGPSDP::getInstance($sattle);
	if ($sattle->flags & Predict_SGPSDP::DEEP_SPACE_EPHEM_FLAG) {
	    $sgpsdp->SDP4($sattle, $sattle->tsince);
	} else {
	    $sgpsdp->SGP4($sattle, $sattle->tsince);
	}
	
	Predict_Math::Convert_Sat_State($sattle->pos, $sattle->vel);
	Predict_SGPObs::Calculate_LatLonAlt($sattle->jul_utc, $sattle->pos, $sat_geodetic);
	
	while ($sat_geodetic->lon < -3.1415926535898) {
	    $sat_geodetic->lon += 6.2831853071796;
	}
	
	while ($sat_geodetic->lon > (3.1415926535898)) {
	    $sat_geodetic->lon -= 6.2831853071796;
	}
	$sat= str_replace('ISS-DATA','ISS',$sat);
	$sat= str_replace('ISS-FM','ISS',$sat);
	if ($old<>$sat) {
		echo str_replace('-','_',$sat) .';';
		echo number_format(round( Predict_Math::Degrees($sat_geodetic->lat),1),1,'.',1).';';
		echo number_format(round(Predict_Math::Degrees($sat_geodetic->lon),1),1,'.',1).';';
		echo number_format(round($sat_geodetic->alt,0),0,',',' ').';';
		$h=$sat_geodetic->alt;
		$rh=(6375+$h);
		$thetarad=acos(6375/$rh);
		$theta = ($thetarad * 57.2958);
		$theta=(($theta*10)+0.5)/10;
		$md=round(220*$theta,0);
		$vel = sqrt(398600/$rh);
		$vel = intval(($vel*100)+0.5, 10)/100;
		$t= intval((6.284*$rh)*(sqrt($rh/398600))/60, 10);	
		$vis = ((2*$theta)/360)*$t;
		$vis = intval(($vis*10)+0.5, 10)/10;
		echo number_format($md,0,',',' ').';'.number_format($vel,1,',',' ').';'.number_format($vis,1,',',' ')."\n";
//		print_r($sat_geodetic);
	}
	$old=$sat;
}