<?php
include('/home/satfg8ojco/www/script_connect.php');


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
	

$utc=$_GET['u'];
if (strlen($utc)>0) setcookie("utc",$utc,time()+3600*24*30);
if (strlen($utc)==0) $utc=$_COOKIE["utc"];
if (strlen($utc)==0) $utc='Etc/GMT+4';


//echo $output.'/'.$sat;exit();

$sql='SELECT * FROM sat WHERE ident="'.$sat.'"';
$rs=mysql_query($sql);
$j=mysql_fetch_array($rs);
$sat_id=$j['id'];
$active=true;
if ($j['etat']==0) $active=false;

if (($output!=='WEBCAL')||(valid_locator($locator)==0)) {
	echo '
<html>
<head>
<link rel="stylesheet" href="/bootstrap/css/bootstrap.css">
<link href="/css/bootstrap-responsive.css" rel="stylesheet" media="screen">
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="/js/bootstrap.min.js"></script>
<link rel="shortcut icon" href="/favicon.ico" >
<link rel="stylesheet" type="text/css" media="all" href="/satellite.css" />
<meta name=viewport content="width=device-width, initial-scale=1">
<title>' .$sat.' Amateur Radio Satellite</title>
</head
<body>';
	include('/home/satfg8ojco/www/header.php');
	
	$m='<div id="tmenu"><div id="menu"><form action="/'.$sat.'" method="GET">
	<div id="smenu">
	Satellite : <select onchange="document.location=\'/\'+this.options[this.selectedIndex].value.toLowerCase();"  name="s">';
	
	$sql='SELECT ident,etat FROM sat ORDER BY ident ASC;';
	$rs=mysql_query($sql);
//print_r($rs);

	while ($js=mysql_fetch_array($rs)) {
		$m.= '<option VALUE="'.$js[0].'" ';
		if ($js[0]==$sat) $m.= ' selected ';
		$m.= '>'.$js[0];
		if ($js[1]==0) {
			$m.= ' *** INACTIVE ***';
		}
		$m.= '</option>';	

	}
	$m.= '</select><br/>
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
	$m.= '</select></div><div id="smenu">';
	$m.= '<select name="v">';
	$m.= '<option value="1" ';
	if ($verif==1) $m.= ' selected ';
	$m.='>Show passes if satellite is active</option>';
	$m.= '<option value="0" ';
	if ($verif==0) $m.= ' selected ';
	$m.='>Show all passes</option>';
	$m.= '</select><br/>';
	$m.= 'Gridsquare (QRA locator) : <input type="text"  style="width:50px;" name="l" value="'.$locator.'" /><br/>
	Altitude (Metres above sea level) : <input type="text" name="a" value="'.$altitude.'" style="width:50px;"/> meters<br/>
	Timezone : <select name="u">';
	
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
	Min time <select name="it">';
	for ($i=0;$i<=24;$i++) {
		$m.= '<option ';
		if ($it==$i) $m.= ' selected ';
		$m.= 'value="'.$i.'">'.$i.':00</option>';
	}
	$m.= '</select> 
	<br/>Max time <select name="at">';
	for ($i=0;$i<=24;$i++) {
		$m.= '<option ';
		if ($at==$i) $m.= ' selected ';
		$m.= 'value="'.$i.'">'.$i.':00</option>';
	}
	$m.= '</select> 
	<br/><br/>Output type : <select name="o"><option>HTML</option><option value="HWEBCAL">WEBCAL</option></select><br/>
	<input type="submit" name="" value="LOAD PREDICTIONS" />
	</div></form></div></div>';
}

if (strlen($output)>0) {
	
	$eol = "\r\n";
	$load = "BEGIN:VCALENDAR" . $eol .
	"VERSION:2.0" . $eol .
	"NAME:PASSAGE SO-50" . $eol .
	"PRODID:-//'.$sat.' v1.0//EN" . $eol .
	"CALSCALE:GREGORIAN" . $eol ;
	
	
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
	$predict->timeRes    = 5; // Pass details: time resolution in seconds
	$predict->numEntries = 200; // Pass details: number of entries per pass
	$predict->threshold  = 360; // Twilight threshold (sun must be at this lat or lower)
//print_r($sattle);	
	// Get the passes and filter visible only, takes about 4 seconds for 10 days
	$results  = $predict->get_passes($sattle, $qth, $now,7);
//print_r($results);
	$filtered = $predict->filterVisiblePasses($results);
	
	//$zone   = 'America/Los_Angeles';
	$format = 'd/m/Y H:i';         // Time format from PHP's date() function
	$format2 = 'Y-m-d H:i:s';         // Time format from PHP's date() function
	
	
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
		if ($output=='HTML') {
			$p.= '<div id="prediction2">';
			$p.='<a target="_blank" href="/satgif.php?id='.$id.'"><img align="right" width="150" height="150" src="/satgif.php?id='.$id.'"></A><br/>';
		}
		$p.='AOS Time : ' . Predict_Time::daynum2readable($pass->aos, $utc, $format) . "\\n";
		$p.= "Duration : ". gmdate("i\m\i\\n",round( daynum2unix($pass->los)-daynum2unix($pass->aos)))."\\n";
		$p.= "AOS Az : " . round($pass->aos_az). ' ('. $predict->azDegreesToDirection($pass->aos_az) . ")\\n";
	//    echo "Max Time: " . Predict_Time::daynum2readable($pass->tca, $zone, $format) . "\\n";
	//    echo "Max Az: " . round($pass->max_el_az).' ('.$predict->azDegreesToDirection($pass->max_el_az) . ")\n";
		$p.= "Max El : " . round($pass->max_el) . "\\n";
		$p.="LOS Time : " . Predict_Time::daynum2readable($pass->los, $utc, $format) . "\\n";
		$p.="LOS Az : " . round($pass->los_az). ' ('.$predict->azDegreesToDirection($pass->los_az) . ")\\n";
	//echo $p.' -> '. intval($aos);
		if ($output=='HTML') {
			$p.='<br/></div>';
		}
		$start=strtotime(Predict_Time::daynum2readable($pass->aos, $utc, $format2));
		$end=strtotime(Predict_Time::daynum2readable($pass->los, $utc, $format2));
	
		$title='PASS '.$sat.' '.Predict_Time::daynum2readable($pass->aos, $utc, 'H\hi');
		if ($active==true) $load.="BEGIN:VEVENT" . $eol .
	        "UID:" . $start . $eol .
	        "DESCRIPTION;ENCODING=quoted-printable:" . htmlspecialchars($p) . $eol .
	        "SUMMARY:" . htmlspecialchars($title) . $eol .
	        "DTSTART;TZID=".$utc.":" . dateToCal($start) . $eol .
	        "DTEND;TZID=".$utc.":" . dateToCal($end) . $eol .
	        "DTSTAMP:" . dateToCal(time()) . $eol .
	        "BEGIN:VALARM". $eol .
	        "TRIGGER:-PT30M". $eol .
	        "ACTION:DISPLAY". $eol .
	        "END:VALARM". $eol .
	        "END:VEVENT" . $eol ;
		
		if (($iapass==true) && ($aapass==true) && ($it<=$aos) && ($at>$aos) ) {
			$html.=str_replace("\\n",'<br/>',$p) .'';
			$totalpasses++;
		}
	} 
	$load.="END:VCALENDAR";
	
	$filename="Event-".$id;
	
	if ($output=='WEBCAL') {
	//echo "WEBC***";
		header('Content-type: text/calendar; charset=utf-8');
		header('Content-Disposition: attachment; filename=' . $filename);
		echo $load;
	}
	else {
		if ($output=='HWEBCAL') {
		echo '<h1>Use this link to import this calendar : </h1><h3><pre>';
		echo 'http://'.$_SERVER['HTTP_HOST']. '/sat.php?s='.$sat.'&e='.$elevation.'&ia='.$ia.'&aa='.$aa.'&l='.$locator.'&a='.$altitude.'&u='.urlencode($utc).'&it='.$it.'&at='.$at.'&o=WEBCAL';
		echo '</pre></h3>';
		}

	}
}

if (strlen($sat)>0)  {
	echo '<div id="result">';		
 		if (file_exists('/home/satfg8ojco/www/images/'.$sat.'.png')) {echo '<img align="right" src="/images/'.$sat.'.png">';}
 		echo '<h1>' .$sat.'</h1>';
 		$band=str_replace('->',' &#10140; ',$j['band']);
 		$tmp=explode('/',$band);
 		if (count($tmp)>1) {
 			$band='<select id="band" onchange="band()">';
 			for($i=0;$i<count($tmp);$i++) {
 				$band.='<option';
 				if ($i==1) $band.=' selected ';
 				$band.='>'.$tmp[$i].'</option>';
 			}
 			$band.='</select>';
 			$s='
 			<script>
 			function band() {
 				var e = document.getElementById("band");
 				var eband = e.selectedIndex;
 				var uplink="'.$j['uplink'].'";
 				uplink=uplink.split("/");
 				document.getElementById("uplink").textContent=uplink[eband];
				var downlink="'.$j['downlink'].'";
				downlink=downlink.split("/");
				document.getElementById("downlink").textContent=downlink[eband];
				var beacon="'.$j['beacon'].'";
				beacon=beacon.split("/");
				document.getElementById("beacon").textContent=beacon[eband];
 			}
 			band();
 			</script>';
 		}
 		echo '<table id="infos">';
 		echo '<tr><td>Mode :</td><td colspan="5">'.$j['smode'].'</td></tr><tr><td>Band :</td><td colspan="5">'.$band.'</td></tr><tr><td>Uplink :</td><td colspan="5"><span id="uplink">'.$j['uplink'] . '</span></td></tr><tr><td>Downlink :</td><td  colspan="5"><span id="downlink">'.$j['downlink'] .'</span></td></tr>';
		if (strlen($j['beacon'])>0) echo '<tr><td>Beacon :</td><td  colspan="5"><span id="beacon">'.$j['beacon'].'</span></td></tr>';
 		echo '<tr><td valign="top">Status :</td><td  colspan="5">';
 		if ($active==false) {
 			echo '<span style="color:red;font-weight:bold;">Inactive</span>';
 		} else {
 			echo '<b>Active</b>';
 		}
 		echo ' - Change the status :</td></tr><tr><td colspan="7">';
 		echo '<input style="background-color:#4169E1;" type="button" onclick="status(\'Heard\')" name="status" value=" Active (Uplink and Downlink) "> ';
 		echo ' <input type="button"  style="background-color:yellow;"  onclick="status(\'Telemetry Only\')" name="status" value=" Inactive (Telemetry Only) "> ';		
		echo '<input  style="background-color:red;"  type="button" onclick="status(\'Not Heard\')" name="status" value=" Inactive (Not Heard) ">';
		
		$sql='SELECT * , AVG( etat ) as ae , DATE_FORMAT(DATE( DATETIME ),"%e %b") as jour
		FROM  `sat_amsat` 
		WHERE  `sat` ='.$sat_id.'
		GROUP BY DATE( DATETIME ) 
		ORDER BY DATE( DATETIME ) DESC 
		LIMIT 5';
//echo $sql;
		$rsavg=mysql_query($sql);
		echo '<table style="font-size:12px;background-color:#CCC;margin-top:5px;border-collapse:2px;padding:5px;text-align:center;"><tr>';
		while ($javg=mysql_fetch_array($rsavg)) {
			echo '<td>'.$javg['jour'].'</td>';	
		}
		mysql_data_seek($rsavg, 0);
		echo '</tr><tr>';
		while ($javg=mysql_fetch_array($rsavg)) {
			echo '<td>'.round($javg['ae']*100,0).'%</td>';	
		}
		echo '</tr></table></td></tr>';
		echo '<script>
		function status(st) {';
		if (strlen($_COOKIE["SatCall"])==0) {
			echo 'var SatCall = prompt("Please enter your valid callsign", "'.$_COOKIE["SatCall"].'");';
		} else {
			echo 'var SatCall = "'.$_COOKIE["SatCall"].'"';
		}
		echo '
			if (SatCall != null) {
		        if (confirm("Do you confirm that '.$sat.' is in on status "+st+" ?\nThis information is recording with your IP address : '.$_SERVER['REMOTE_ADDR'].' and Callsign "+SatCall+" and will update AmSat status page !")) {
		           console.log("sat_id='.$sat_id.'&SatCall="+SatCall+"&status="+st);
		           $.ajax({
		              url : "/update_status.php",
		              data:"sat_id='.$sat_id.'&SatCall="+SatCall+"&status="+st,
		              type : "GET",
		              dataType : "html", 
		              success : function(code_html, statut){ // code_html contient le HTML renvoyé
		              		document.cookie="SatCall="+SatCall+"; expires=Thu, 18 Dec '.date('Y',strtotime('+1 year')).' 12:00:00 UTC; path=/";
		              		alert(code_html);
		              }
		           });
		           
		        }
			}
		}
		</script>';
		
 		echo '<tr><td>Latitude :</td><td><span id="lat"></span> &deg;</td>';
 		echo '<td>Longitude :</td><td><span id="lon"></span>&deg;</td>';
 		echo '<td> Altitude :</td><td><span id="alti"></span> km</td></tr>';
 		
 		echo '</table>';
 		
 		echo '<br/><div id="map"></div><div>'.str_replace("\n","\n<br/>",$j['infos']);
 		echo '<br><br>Actual Surface Range : <span id="maxdi"></span> km';
 		echo '<br>Actual speed : <span id="speed"></span> km/s';
 		echo '<br>Maximum Visibility Time : <span id="viz"></span> minutes';
 		echo '</div><br/><br/>';
 		echo '<div style="clear:both;"></div>'.$m;
 		if ($output=='') {
 		
 			echo '<h4>Two-Line Element set (TLE) :</h4>'.str_replace("\n",'<br>', $j['tle']);
 		
 		}
		if ($output=='HTML') {
	
	
		echo '<br/><h2>Predictions for '.$sat.' for next 7 days ('.$totalpasses.' passes)</h2>';}
		if (($active==false) && (strlen($html)>0) ) echo '<br/><h3 style="color:red;">AMSAT status report that '.$sat.' is currently inactive ! Webcal not show any results ! <a style="color:red;" href="http://www.amsat.org/status/" target="_blank">See Amsat for more informations</a>.</h3><br/>';
		
		echo $html ;
}

echo $s .'
<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCeTgyu4YzNaDbm6zPq3EUEeR-FDINI3AE&signed_in=false"></script>
<script>
var lat2=0;
var lon2=0;
var myLatlng = new google.maps.LatLng(0,0);
var myOptions = {
 zoom: 0,
 center: myLatlng,
 disableDefaultUI: true,
 mapTypeId: google.maps.MapTypeId.SATELLITE,
	}
var map = new google.maps.Map(document.getElementById("map"), myOptions);
var marker = new google.maps.Marker({
 position: myLatlng,
 map: map,
 draggable: false,
 icon: "/images/satellite.png"
 });


function moveMarker() {
    
    //delayed so you can see it move
    setTimeout( function(){ 
        marker.setPosition( new google.maps.LatLng( lat2, lon2 ) );
  //      map.panTo( new google.maps.LatLng( lat, lon ) );
    }, 0 );

};



var updatepos=function() {
	$.ajax({
	   url : "/sat2.php",
	   data:"s='.$sat.'",
	   type : "GET",
	   dataType : "html", // On désire recevoir du HTML
	   success : function(code_html, statut){ // code_html contient le HTML renvoyé
	   		var html=code_html.split(";");
	   		var html0=html[1];
	   		var html1=html[2];
	   		var html2=html[3];
	   		var html3=html[4];
	   		var html4=html[5];
	   		var html5=html[6];
	   		if (html0==undefined) html0="0";
	   		if (html1==undefined) html1="0";
	   		if (html2==undefined) html2="0";
	   		if (html3==undefined) html3="0";
	   		if (html4==undefined) html4="0";
	   		if (html5==undefined) html5="0";
	   		lat2=html0;
	   		lon2=html1;
	       document.getElementById("lat").textContent=html0;
	       document.getElementById("lon").textContent=html1;
	       document.getElementById("alti").textContent=html2;
	       document.getElementById("maxdi").textContent=html3;
	       document.getElementById("speed").textContent=html4;
	       document.getElementById("viz").textContent=html5;
	       moveMarker( this.map, this.marker );
	   }
	});
	window.setTimeout(updatepos, 5000);
}


$(document).ready(function(){
updatepos();
//	window.setTimeout(updatepos, 1000);
});

</script>
 <script type="text/javascript">
 
   var _gaq = _gaq || [];
   _gaq.push([\'_setAccount\', \'UA-40218456-1\']);
   _gaq.push([\'_trackPageview\']);
 
   (function() {
     var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;
     ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';
     var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);
   })();
 
 </script>
 </body>
 </html>';	

