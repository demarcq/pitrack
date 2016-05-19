<?php 


function daynum2unix($dn) {
        // Converts a daynum to a UNIX timestamp

        return (86400.0 * ($dn - 2444238.5 + 3651.0));
}


function dateToCal($timestamp) {
  return date('Ymd\TGis', $timestamp);
}

function escapeString($string) {
  return preg_replace('/([\,;])/','\\\$1', $string);
}  

function loc_to_latlon ($loc) {
	/* lat */
	$l[0] = 
	(ord(substr($loc, 1, 1))-65) * 10 - 90 +
	(ord(substr($loc, 3, 1))-48) +
	(ord(substr($loc, 5, 1))-65) / 24 + 1/48;
	//$l[0] = deg_to_rad($l[0]);
	/* lon */
	$l[1] = 
	(ord(substr($loc, 0, 1))-65) * 20 - 180 +
	(ord(substr($loc, 2, 1))-48) * 2 +
	(ord(substr($loc, 4, 1))-65) / 12 + 1/24;
	//$l[1] = deg_to_rad($l[1]);

	return $l;
}

function deg_to_rad ($deg) {
	return (M_PI * $deg/180);
}

function rad_to_deg ($rad) {
	return (($rad/M_PI) * 180);
}

function distance($lat1, $lon1, $lat2, $lon2, $unit) {

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  if ($unit == "K") {
    return ($miles * 1.609344);
  } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
        return $miles;
      }
}


$utc='Etc/GMT';
$locator=$_GET['l'];
if (strlen($locator)==0) $locator='FK96ig';
$locator=strtoupper($locator);

$locator2=$_GET['l2'];
if (strlen($locator2)==0) $locator2='EM34sr';
$locator2=strtoupper($locator2);

$sat=$_GET['s'];
if (strlen($sat)==0) $sat='SO-50';

echo '
<html>
<head>
<link rel="stylesheet" href="/bootstrap/css/bootstrap.css">
<link href="/css/bootstrap-responsive.css" rel="stylesheet" media="screen">
<link rel="stylesheet" type="text/css" media="all" href="/satellite.css" />
<meta name=viewport content="width=device-width, initial-scale=1">
<title>Amateur Radio Satellite Prediction</title>

</head
<body>';
include('/home/satfg8ojco/www/header.php');
echo '<div id="tmenu2"><div id="menu"><form method="GET">
<div id="smenu2">
Satellite : <select name="s">';
$f=scandir('/home/fg8ojcom/tmp/',0);
for ($i=0;$i<count($f);$i++) {
	$t=$f[$i];
//		echo $t . '/'.substr($t,strlen($t)-4);
	if (substr($t,strlen($t)-4)=='.tle') {
		echo '<option VALUE="'.substr($t,0,strlen($t)-4).'" ';
		if (substr($t,0,strlen($t)-4)==$sat) echo ' selected ';
		echo '>'.substr($t,0,strlen($t)-4);
		if (file_exists('/home/fg8ojcom/tmp/'.substr($t,0,strlen($t)-4).'.ok')==false) {
			echo ' *** INACTIVE ***';
		}
		echo '</option>';	
	}

}
echo '</select> ';
echo 'Your Gridsquare : <input type="text"  style="width:50px;" name="l" value="'.$locator.'" />';
echo ' Sked Gridsquare : <input type="text"  style="width:50px;" name="l2" value="'.$locator2.'" />';
echo ' <input type="submit" name="OK" value="LOOKING FOR PASSES" /></div></div></div>';

if (strlen($_GET['OK'])>0) {
	date_default_timezone_set('UTC');
	
		require_once 'Predict.php';
	require_once 'Predict/Sat.php';
	require_once 'Predict/QTH.php';
	require_once 'Predict/Time.php';
	require_once 'Predict/TLE.php';
	
	$predict  = new Predict();
	$qth      = new Predict_QTH();
	$qth->alt = 0;
	$qth->lat = loc_to_latlon($locator)[0];
	$qth->lon = loc_to_latlon($locator)[1];
		
	$tleFile = file('/home/fg8ojcom/tmp/'.$sat.'.tle'); // Load up the ISS data file from NORAD
	$tle     = new Predict_TLE($tleFile[0], $tleFile[1], $tleFile[2]); // Instantiate it
	$sattle     = new Predict_Sat($tle); // Load up the satellite data
	$now     = Predict_Time::get_current_daynum(); // get the current time as Julian Date (daynum)
	
	// You can modify some preferences in Predict(), the defaults are below
	//
	$predict->minEle     = 0; // Minimum elevation for a pass
	$predict->timeRes    = 5; // Pass details: time resolution in seconds
	$predict->numEntries = 100; // Pass details: number of entries per pass
	$predict->threshold  = 90; // Twilight threshold (sun must be at this lat or lower)
	
	// Get the passes and filter visible only, takes about 4 seconds for 10 days
	$results  = $predict->get_passes($sattle, $qth, $now,60);
	//print_r($results);
	$filtered = $predict->filterVisiblePasses($results);
	
	//$zone   = 'America/Los_Angeles';
	$format = 'd/m/Y';         // Time format from PHP's date() function
	$format2 = 'H:i';         // Time format from PHP's date() function
	
	
	$predict2  = new Predict();
	$qth2      = new Predict_QTH();
	$qth2->alt = $elevation;
	$qth2->lat = loc_to_latlon($locator2)[0];
	$qth2->lon = loc_to_latlon($locator2)[1];
	
	
	echo '<div id="prediction2">';
	echo '<bR/><h2>Distance : '.round(distance($qth->lat, $qth->lon,$qth2->lat, $qth2->lon, 'K'))  .'km ( '.round(distance($qth->lat, $qth->lon,$qth2->lat, $qth2->lon, 'M')).' Miles) </h2>';
	echo '<bR/></div>';
	
	
	
	$predict2->minEle     = 0; // Minimum elevation for a pass
	$predict2->timeRes    = 5; // Pass details: time resolution in seconds
	$predict2->numEntries = 100; // Pass details: number of entries per pass
	$predict2->threshold  = 90; // Twilight threshold (sun must be at this lat or lower)
	
	// Get the passes and filter visible only, takes about 4 seconds for 10 days
	$results2  = $predict2->get_passes($sattle, $qth2, $now,60);
	//print_r($results);
	$filtered2 = $predict->filterVisiblePasses($results2);
	$r=false;
	$jour='';
	foreach ($results as $pass) {
		
		$pg=false;
		$me=0;
		$me2=0;
		$dme=0;
		$dme2=0;
		
//print_r($pass);exit();		
		foreach ($results2 as $pass2) {			
			foreach ($pass2->details as $dt2) {
				if (($dt2->time>=$pass->aos) && ($dt2->time<=$pass->los)) {
					$pg=true;
					if ($me2<$dt2->el) {
					 	$me2=$dt2->el;
					 	$dme2=$dt2->time;
					}
				}
			}
			if ($pg==true) break;
		}
		foreach ($pass->details as $dt) {
			if (($dt->time>=$pass2->aos) && ($dt->time<=$pass2->los)) {
				if ($me<$dt->el) {
				 	$me=$dt->el;
				 	$dme=$dt->time;
				}
			}
		}
		if ($pg==true) {
		
		$r=true;
		$aos=$pass->aos;
		if ($pass->aos<$pass2->aos) $aos=$pass2->aos;
		$los=$pass->los;
		if ($pass->los>$pass2->los) $los=$pass2->los;
		echo '<div id="prediction2">';
		$jour2=Predict_Time::daynum2readable($aos, $utc, $format);
		if ($jour<>$jour2) { 
			echo '<br/><b>'.$jour2.'</b>';
			$jour=$jour2;
		}
		echo '<br>Sked duration : '.Predict_Time::daynum2readable(($los-$aos), $utc, '00:i:s');
		echo '<br>Sked AOS : '.Predict_Time::daynum2readable($aos, $utc, $format2);
		echo '<br>Sked LOS : '.Predict_Time::daynum2readable($los, $utc, $format2);
		echo '<br>Max elevation for you : '.round($me) . '&deg; (at ' .Predict_Time::daynum2readable($dme, $utc, $format2) .')';
		echo '<br>Max elevation for sked : '.round($me2) . '&deg; (at '.Predict_Time::daynum2readable($dme2, $utc, $format2).')';
		/*
		echo '<br>AOS l :'.Predict_Time::daynum2readable($pass->aos, $utc, $format);
		echo '<br>LOS l :'.Predict_Time::daynum2readable($pass->los, $utc, $format);
		echo '<br>AOS l2 :'.Predict_Time::daynum2readable($pass2->aos, $utc, $format);
		echo '<br>LOS l2 :'.Predict_Time::daynum2readable($pass2->los, $utc, $format);
		*/
		echo '<br/><br/></div>';
		$pg=false;
		}
	}
	if ($r==false) echo '<div id="prediction2"><br/><br/><h2>Sorry, sked is impossible on '.$sat.'</h2></div>'; 
}

echo '<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push([\'_setAccount\', \'UA-40218456-1\']);
  _gaq.push([\'_trackPageview\']);

  (function() {
    var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;
    ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';
    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script></body></html>';

 ?>