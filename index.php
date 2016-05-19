<?php
include('script_connect.php');


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
<title>Amateur Radio Satellite</title>
</head>
<body>
';
include('/home/satfg8ojco/www/header.php');

$t=$_GET['t'];
if (strlen($t)==0) $t=0;
$sql='SELECT * FROM sat ';
if ($t=='0') $sql.=' WHERE etat>0 AND LENGTH(tle)>0 ';
$sql.='ORDER BY ident ASC ;';
$rs=$db->query($sql);

echo '<br/><center><select onchange="document.location=\'/?t=\'+this.selectedIndex;"><option value="0"';
if ($t=='0') echo ' selected ';
echo '>Only active satellites</option><option value="1"';
if ($t=='1') echo ' selected ';
echo '>All amateur radio satellites</option></select></h3></center>';

echo '<table style="margin-top:0px;" align="center" width="1000"><tr id="toptable"><td colspan="2">Satellite</td><td>Mode</td><td>Band</td><td>Uplink</td><td>Downlink</td></tr>';
while ($j=$rs->fetchArray()) {
	echo "\n\n".'<tr class ="mode'.$j['mode'] ;
	if ($j['etat']==0) echo ' inactive';
	echo '"><td><img width="20" src="/images/'.$j['etat'].'.png">';
	echo '</td>';
	echo '<td><a ';
	if ($j['etat']==0) echo 'class="inactive" ';
	echo 'href="/'.strtolower($j['ident']).'">'.$j['ident'].'</a></td><td>';
	echo $j['smode'] . '</td><td>';
	$band=str_replace('->',' &#10140; ',$j['band']);
	$tmp=explode('/',$band);
	
	if (count($tmp)>1) {
		$band='<select class="mode'.$j['mode'].'" id="band" onchange="band()">';
		for($i=0;$i<count($tmp);$i++) {
			$band.='<option ';
			if ($i==0) $band.= 'selected';
			$band.='>'.$tmp[$i].'</option>';
		}
		$band.='</select>';
		$s='<script>
		function band() {
			var e = document.getElementById("band");
			var eband = e.selectedIndex;
			var uplink="'.$j['uplink'].'";
			uplink=uplink.split("/");
			document.getElementById("uplink'.$j['id'].'").textContent=uplink[eband];
			var downlink="'.$j['downlink'].'";
			downlink=downlink.split("/");
			document.getElementById("downlink'.$j['id'].'").textContent=downlink[eband];
		}
		band();
		</script>';
	}		
	echo $band . '</td><td>';
	echo '<span id="uplink'.$j['id'].'">'.$j['uplink'] . '</span></td><td>';
	echo '<span id="downlink'.$j['id'].'">'. $j['downlink'] . '</span></td>';
	echo '</tr>';
}
echo '</table>';
echo $s;
echo '<br/><table align="center" style="widht:400px;text-align:center;"><tr><td class="mode0">DATA</td><td class="mode1">FM</td><td class="mode2">SSB</td><td><img width="20" src="/1.png">Active satellite</td>';
if ($t=='1') echo '<td class="inactive"><img width="20" src="/0.png">Inactive satellite</td>';
echo '</tr></table>';
echo '<div id="map2"></div>';
//echo '<div style="width:1000px;margin:auto;padding-top:20px;">This website is </div>';
echo '
<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCeTgyu4YzNaDbm6zPq3EUEeR-FDINI3AE&signed_in=false"></script>
<script>
var lat2=0;
var lon2=0;
var myLatlng = new google.maps.LatLng(0,0);
var myOptions = {
 zoom: 2,
 center: myLatlng,
 disableDefaultUI: true,
 mapTypeId: google.maps.MapTypeId.SATELLITE,
 scrollwheel: false,
     navigationControl: false,
     mapTypeControl: false,
     scaleControl: false,
     draggable: false,
	}
var map = new google.maps.Map(document.getElementById("map2"), myOptions);

';
$i=0;
$old='';
//mysql_data_seek($rs, 0);
while ($j=$rs->fetchArray()) {
	$sat=str_replace('-','_',$j['ident']);
	$sat=str_replace('ISS_FM','ISS',$sat);
	$sat=str_replace('ISS_DATA','ISS',$sat);
	if ($old<>$sat) {
	echo '
	var info'.$sat.' = new google.maps.InfoWindow({
	           content: "<a href=\"/'.$j['ident'].'\">'.str_replace('_','-',$sat).'</a>"
	         });
 var marker'.$sat.' = new google.maps.Marker({
 position: myLatlng,
 map: map,
 draggable: false,
 title: "'.$j['ident'].'",
 icon: "/images/satellite.png"
});
info'.$sat.'.open(map,marker'.$sat.');	
'; 
	}
	$old=$sat;
} 
echo '

var updatepos=function() {
	$.ajax({
	   url : "/sat2.php",
	   data:"t='.$t.'",
	   type : "GET",
	   dataType : "html", // On désire recevoir du HTML
	   success : function(code_html, statut){ // code_html contient le HTML renvoyé
	   		var sats=code_html.split("\n");
	   		for (i=0;i<sats.length;i++) {
			var pos=sats[i].split(";");
			lat2=pos[1];
			lon2=pos[2];
	        window["marker"+pos[0]].setPosition( new google.maps.LatLng( lat2, lon2 ) );
	      }
	   }
	});
	window.setTimeout(updatepos, 5000);
}


$(document).ready(function(){
updatepos();
//	window.setTimeout(updatepos, 1000);
});

</script>
';
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