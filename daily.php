<?php
include('/home/satfg8ojco/www/script_connect.php');

$output=$_GET['o'];

$locator=strtoupper($_GET['l']);
if (strlen($locator)>0) setcookie("locator",$locator,time()+3600*24*30);
if ((strlen($locator)==0) && (strlen($_COOKIE["locator"])>0) ) {
	$locator=$_COOKIE["locator"];
	header('location:/daily.php?l='.$locator,302);
	exit();
}
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


$verif=$_GET['v'];
if (strlen($_GET['v'])>0) {
	setcookie("verif",$verif,time()+3600*24*30);
} else {
	if (strlen($_COOKIE["verif"])>0) $verif=$_COOKIE["verif"];
}

	

$utc=$_GET['u'];
if (strlen($utc)>0) setcookie("utc",$utc,time()+3600*24*30);
if (strlen($utc)==0) $utc=$_COOKIE["utc"];
if (strlen($utc)==0) $utc='GMT';


if ($output!=='WEBCAL') echo '
<html>
<head>
<meta http-equiv="refresh" content="60">
<meta name=viewport content="width=device-width, initial-scale=1">
<title>Amateur Radio Satellite Prediction</title>
<link rel="stylesheet" href="/bootstrap/css/bootstrap.css">
<link href="/css/bootstrap-responsive.css" rel="stylesheet" media="screen">
<link rel="stylesheet" type="text/css" media="all" href="/satellite.css" />
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="/js/bootstrap.min.js"></script>
</head
<body>
';
if ($output!='WEBCAL') include('/home/satfg8ojco/www/header.php');

$m='<div id="tmenu"><div id="menu"><form action="/daily.php?l='.$locator.'" method="GET">
<div id="smenu">
Min Satellite Elevation : <input type="text" name="e" value="'.$elevation.'" style="width:30px;"/> &deg;<br/>
Min Azimut : <select name="ia">';
for ($i=0;$i<36;$i++){
	$m.= '<option ';
	if ($ia==($i*10)) $m.= ' selected ';
	$m.= ' value="'.($i*10).'">'.($i*10).' &deg;</option>';
}
$m.= '</select><br/>
Max Azimut : <select name="aa">';
for ($i=0;$i<36;$i++){
	$m.= '<option ';
	if ($aa==(($i+1)*10)) $m.= ' selected ';
	$m.= 'value="'.(($i+1)*10).'">'.(($i+1)*10).' &deg;</option>';
}
$m.= '</select><br/>';
$m.= '</div><div id="smenu"><select name="v">';
$m.= '<option value="1" ';
if ($verif==1) $m.= ' selected ';
$m.='>Show passes if satellite is active</option>';
$m.= '<option value="0" ';
if ($verif==0) $m.= ' selected ';
$m.='>Show all passes</option>';
$m.= '</select><br/>

Gridsquare (QRA locator) : <input type="text"  style="width:80px;" name="l" value="'.$locator.'" /><br/>
Altitude (Meters above sea level) : <input type="text" name="a" value="'.$altitude.'" style="width:50px;"/><br/>
Timezone :<br/><select name="u">';

$zonesl = timezone_identifiers_list();
foreach ($zonesl as $zone) 
{
	//$zone = explode('/', $zone);
	$m.= '<option';
	if ($utc==$zone) $m.= ' selected ';
	$m.= '>';
	$m.= $zone;
	$m.= '</option>';
}

for ($i=-12;$i<=12;$i++) {
	$m.= '<option ';
	$zt='Etc/GMT';
	if ($i>0) $zt.='+';
	$zt.=$i;
	if ($utc==$zt) $m.= ' selected ';
	$m.= '">Etc/GMT';
	if (strpos($i,'-')===false) $m.= '+';
	$m.= $i.'</option>';
}
$m.= '</select></div><div id="smenu">
Min time :<br/><select name="it">';
for ($i=0;$i<25;$i++) {
	$m.= '<option ';
	if ($it==$i) $m.= ' selected ';
	$m.= 'value="'.$i.'">'.$i.':00</option>';
}
$m.= '</select> 
<br/>Max time :<br/><select name="at">';
for ($i=0;$i<25;$i++) {
	$m.= '<option ';
	if ($at==$i) $m.= ' selected ';
	$m.= 'value="'.$i.'">'.$i.':00</option>';
}
$m.= '</select><br/>
Output type : <select name="o"><option ';
if ($output=='HTML') $m.='selected';
$m.='>HTML</option><option ';
if ($output=='HWEBCAL') $m.='selected';
$m.= ' value="HWEBCAL">WEBCAL</option></select>

</div><input type="submit" name="" style="width:100%;height:2em;" value="LOAD PREDICTIONS" /></div></form></div>';

if ($output<>'WEBCAL') echo $m;

/*
echo '<form method="GET"><table align="center"><tr><td>Your Grid :<br><font size="-2"><a href="http://qthlocator.free.fr/" target="_blank">6 caracters long QTH Locator</a></font></td><td><input type="text" name="l" value="'.$locator.'" /></td><td><input type="submit" value=" Get my next passes "></td></tr></table></form>';
*/
//echo $output.'/'.$sat;exit();

if (strlen($_GET['l'])==0) exit();

$predi = array();
$predicti=0;

$sql='SELECT * FROM sat WHERE LENGTH(tle)>0 ';
if ($verif==1) $sql.=' AND etat=1 ;';
$rs=mysql_query($sql);
while ($j=mysql_fetch_array($rs)) {
	
	$active=true;
	if ($j['etat']==0) $active=false;
	$html='';
	date_default_timezone_set($utc);
	
	require_once 'Predict.php';
	require_once 'Predict/Sat.php';
	require_once 'Predict/QTH.php';
	require_once 'Predict/Time.php';
	require_once 'Predict/TLE.php';
	
	$predict  = new Predict();
	$qth      = new Predict_QTH();
	$qth->alt = $altitude; // Altitude in meters
	$qth->lat = loc_to_latlon($locator)[0];
	$qth->lon = loc_to_latlon($locator)[1];
	$tleFile = explode("\r\n",$j['tle']); // Load up the ISS data file from NORAD
//print_r($qth);
//echo "\n".print_r($tleFile)."\n";
	$tle     = new Predict_TLE($tleFile[0], $tleFile[1], $tleFile[2]); // Instantiate it
//print_r($tle);
	$sattle     = new Predict_Sat($tle); // Load up the satellite data
	$now     = Predict_Time::get_current_daynum(); // get the current time as Julian Date (daynum)
	
	// You can modify some preferences in Predict(), the defaults are below
	//
	$predict->minEle     = $elevation; // Minimum elevation for a pass
	$predict->timeRes    = 60; // Pass details: time resolution in seconds
	$predict->numEntries = 10; // Pass details: number of entries per pass
	$predict->threshold  = 90; // Twilight threshold (sun must be at this lat or lower)
	
	// Get the passes and filter visible only, takes about 4 seconds for 10 days
	$duree=1;
	if ($output=='WEBCAL') $duree=14;
	$results  = $predict->get_passes($sattle, $qth, $now,$duree);
	//print_r($results);
	$filtered = $predict->filterVisiblePasses($results);
	
	//$zone   = 'America/Los_Angeles';
	$format = 'd/m/Y H:i';         // Time format from PHP's date() function
	$format2 = 'Y-m-d H:i:s';         // Time format from PHP's date() function
	$formath = 'H:i'; 
	
	$totalpasses=0;
	//echo count($filtered);
	// Format the output similar to the heavens-above.com website
	foreach ($results as $pass) {
	//print_r($pass->details);
		$img='';
		$id=randomPassword(20);
		$iapass=false;
		$aapass=false;
		$hp=round(count($pass->details)/10,0);
		for ($i=0;$i<count($pass->details);$i++) {
			$azt=$pass->details[$i]->az;
			if (((count($pass->details)-1)==$i)||($img=='')) {
				if ($img=='') {
				$img.='AOS,';
			} else {
				$img.='LOS,';
			} }
			else {
				if (round($i/24,0)==$i/24) {
					$img.=Predict_Time::daynum2readable($pass->details[$i]->time, $utc, 'H  i') .',';
				}
				else {
					$img.=',';		
				}
			}
			$img.=$azt.','.$pass->details[$i]->el."\n";
			if ($ia<$aa) {
				if ($azt>$ia){ $iapass=true;}
				if ($azt<$aa){ $aapass=true;}
			} else {
				if ($azt>$ia){ $aapass=true;$iapass=true;}
				if ($azt<$aa){ $aapass=true;$iapass=true;}
			}
		}
		if ($output=='HTML') {
			$fi = fopen('/home/satfg8ojco/tmp/'.$id.'.img', 'w');
			fputs($fi,$img);
			fclose($fi);
	//echo $img."\n\
		}
		$aos=intval(Predict_Time::daynum2readable($pass->aos, $utc, 'G'));
		$p='';
		$p.= '<div id="prediction2">';
	//	$p.='<a target="_blank" href="/satgif.php?id='.$id.'"><img align="right" width="150" height="150" src="/satgif.php?id='.$id.'"></A><br/>';
		
		
		if (($iapass==true) && ($aapass==true) && ($it<=$aos) && ($at>$aos) ) {
			$predicti++;
			$predi[$predicti]['mode']=$j['mode'];
			$predi[$predicti]['etat']=$j['etat'];
			$predi[$predicti]['daos']=Predict_Time::daynum2readable($pass->aos, $utc, 'U');
			$predi[$predicti]['sat']=$j['ident'];
			$predi[$predicti]['aos']=Predict_Time::daynum2readable($pass->aos, $utc, $formath);
			$predi[$predicti]['aosz']=strtotime(Predict_Time::daynum2readable($pass->aos, $utc, $format2));
			$predi[$predicti]['aosd']=Predict_Time::daynum2readable($pass->aos, $utc, 'd/m/Y');
			$predi[$predicti]['duration']=gmdate("i \m\i\\n",round( daynum2unix($pass->los)-daynum2unix($pass->aos)));
			$predi[$predicti]['aosaz']=round($pass->aos_az). ' ('. $predict->azDegreesToDirection($pass->aos_az).')';
			$predi[$predicti]['maxel']=round($pass->max_el);
			$predi[$predicti]['los']=Predict_Time::daynum2readable($pass->los, $utc, $formath);
			$predi[$predicti]['losz']=strtotime(Predict_Time::daynum2readable($pass->los, $utc, $format2));
			$predi[$predicti]['losd']=Predict_Time::daynum2readable($pass->los, $utc, 'd/m/Y');
			$predi[$predicti]['losaz']=round($pass->los_az). ' ('.$predict->azDegreesToDirection($pass->los_az) .')' ;
		
			$p.='AOS Time (UTC) : ' . Predict_Time::daynum2readable($pass->aos, $utc, $format) . "\\n";
			$p.= "Duration : ". gmdate("i\m\i\\n",round( daynum2unix($pass->los)-daynum2unix($pass->aos)))."\\n";
			$p.= "AOS Az : " . round($pass->aos_az). ' ('. $predict->azDegreesToDirection($pass->aos_az) . ")\\n";
		//    echo "Max Time: " . Predict_Time::daynum2readable($pass->tca, $zone, $format) . "\\n";
		//    echo "Max Az: " . round($pass->max_el_az).' ('.$predict->azDegreesToDirection($pass->max_el_az) . ")\n";
			$p.= "Max El : " . round($pass->max_el) . "\\n";
			$p.="LOS Time (UTC) : " . Predict_Time::daynum2readable($pass->los, $utc, $format) . "\\n";
			$p.="LOS Az : " . round($pass->los_az). ' ('.$predict->azDegreesToDirection($pass->los_az) . ")\\n";
		//echo $p.' -> '. intval($aos);
			if ($output=='HTML') {
				$p.='<br/></div>';
			}
			$start=strtotime(Predict_Time::daynum2readable($pass->aos, $utc, $format2));
			$end=strtotime(Predict_Time::daynum2readable($pass->los, $utc, $format2));
		
			$title='PASS '.$sat.' '.Predict_Time::daynum2readable($pass->aos, $utc, 'H\hi');
			$totalpasses++;
		}
	}
	
	
			
		
	
		
//		echo '<h2>Predictions for '.$j['ident'].' for next day ('.$totalpasses.' passes)</h2>';
	 	

	
//echo $html ;	

}


function cmp($a, $b)
{
    return ($b['daos'] > $a['daos']) ? -1 : 1;
}

usort($predi, "cmp");
//print_r($predi);
if ($output=='WEBCAL') {
	header('Content-type: text/calendar; charset=utf-8');
	header('Content-Disposition: attachment; filename=' . $filename);
	$load.="END:VCALENDAR";
	$eol = "\r\n";
	$load = "BEGIN:VCALENDAR" . $eol .
	"VERSION:2.0" . $eol .
	"NAME:PASSES PREDICATOR FG8OJ" . $eol .
	"PRODID:-//FG8OJ SAT PREDICATOR v1.0//EN" . $eol .
	"CALSCALE:GREGORIAN" . $eol ;
	
	foreach ($predi as $key => $v) {
		$p='AOS : '.$v['aos']. "\\n";
		$p.= 'Duration : '.$v['duration']. "\\n";
		$p.= 'AOS Azimut : '.$v['aosaz']. "\\n";
		$p.= 'Mal elevation : '.$v['maxel']. "\\n";
		$p.= 'LOS : '.$v['los']. "\\n";
		$p.= 'LOS Azimut : '.$v['losaz']. "\\n";
		$title='PASS '.$v['sat'].' '.$v['aos'];
		
		if (($verif==0) || ($verif==1) && ($v['etat']==true)) $load.="BEGIN:VEVENT" . $eol .
		    "UID:" . $v['sat'] .'-' . $v['aos'] . $eol .
		    "DESCRIPTION;ENCODING=quoted-printable:" . htmlspecialchars($p) . $eol .
		    "SUMMARY:" . htmlspecialchars($title) . $eol .
		    "DTSTART;TZID=".$utc.":" . dateToCal($v['aosz']) . $eol .
		    "DTEND;TZID=".$utc.":" . dateToCal($v['losz']) . $eol .
		    "DTSTAMP:" . dateToCal(time()) . $eol .
		    "BEGIN:VALARM". $eol .
		    "TRIGGER:-PT30M". $eol .
		    "ACTION:DISPLAY". $eol .
		    "END:VALARM". $eol .
		    "END:VEVENT" . $eol ;
	}
	$load.="END:VCALENDAR";
	
	$filename="Event-".$id;
	echo $load;
}
else {
	if ($output=='HWEBCAL') {
		echo '<center><h3>Use this link to import this calendar : </h3><h3><pre>';
		echo 'http://'.$_SERVER['HTTP_HOST']. '/daily.php?o=WEBCAL&e='.$elevation.'&ia='.$ia.'&aa='.$aa.'&l='.$locator.'&a='.$altitude.'&u='.urlencode($utc).'&it='.$it.'&at='.$at;
		echo '</pre></h3></center><div style="width:800px;margin:auto;"><h4>Example of Satellites passes on my calendar :</h4><img src="/images/calendar.png"><br/><br/>
		<h4>Import in Google Calendar :</h4> Add by URL item in the "Add" menu under "Other calendars" (to the left of the main calendar grid).<br/><img src="/images/gcalendar.png"><br/>
		<br/><h4>Import in Mac Calendar :</h4> choose File > New Calendar Subscription, and in the sheet that appears enter the copied URL and click Subscribe<br/><img src="/images/calendar.jpg"></div>';
		} else {
		echo '<table align="center" width="800"><tr id="toptable"><td>Satellite</td><td>AOS Time</td><td>Duration</td><td>AOS az</td><td>Max El</td><td>LOS Time</td><td>LOS Az</td></tr>';
		$daos='';
		$jour='';
		foreach ($predi as $key => $v) {
			$aos =$v['aos'];
			if ($aosd=='') $daos=$v['aosd'];
			if ($jour<>$v['aosd']) {
				$jour=$v['aosd'];
				echo '<tr><td colspan="7" style="background-color:white;color:black;text-align:center;font-weight:bold;">- - - - - - - '.$jour.' - - - - - - -</td></tr>';
			}
			else {
				$aos=substr($aos,strpos($aos,$aosd)+strlen($aosd));
				for($i=0;$i<strlen($aosd);$i++) {
					//$aos='&nbsp; '.$aos;
				}
			}
			$los =$v['los'];
			echo "\n\n".'<tr id="mode'.$v['mode'].'"><td><a class="';
			if ($v['etat']==0) echo 'inactive';
			echo '" href="/'.strtolower($v['sat']).'?e='.$elevation.'&ia='.$ia.'&aa='.$aa.'&v=';
			if ($verif==true) {
				echo '1';
			}
			else {
				echo '0';		
				}
			echo '&l='.$locator.'&a='.$altitude.'&u='.urlencode($utc).'&it='.$it.'&at='.$at.'&o=HTML">'.$v['sat'].'</a></td><td>'.$aos ;
			echo '</td><td>'.$v['duration'];
			echo '</td><td>'.$v['aosaz'];
			echo '</td><td>'.$v['maxel'];
			echo '</td><td>'.$los;
			echo '</td><td>'.$v['losaz'];
		}
	}
}
if ($output<>'WEBCAL') echo '</table><script type="text/javascript">

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