<?php
include('/home/satfg8ojco/www/script_connect.php');


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

echo '<table width="1000" align="center" style="max-width:1000px;"><tr><td>';
?>

<h3>Antennas for amateur radio satellites</h3>

<h4>Portable or at station ?</h4>
Depending of your budget and your situation : If you have a perfect environnement, free space around, low horizon at 360 degrees and an unlimited budget to buy antennas, rotors and a huge transceiver, just do it !<br/>If not, ask yourself around you if you can easily access to a high point on a mountain not so far from your house... A portable antenna will be a good solution, even to make distance records (ask Dave KG5CCI who have make record on AO-7 - 7947 km and on FO-29 - 7600 km, he use an arrow antenna on his mountain, it is unbelievably effective !).<br />However, it can be both : portable and station ! You'll never miss a satellite ! :)
<br/>
<h4>Portable</h4><br/>
&#10140; <b>Arrow 146/437-10 antenna</b> is certainly the must have to operate in portable ! <a href="http://www.arrowantennas.com/arrowii/146-437.html" target="_blank">Buy one</a><br/>
<img src="/images/arrow.jpg" alt="" />
<br /><br />&#10140; <b>Elk Antennas</b> produce a 2M/440L5 Dual-Band Antenna which is a periodic antenna <a href="https://elkantennas.com/product/dual-band-2m440l5-log-periodic-antenna/" target="_blank">Buy one</a><br/>
<img src="/images/elk.jpg" alt="" />
<br /><br />&#10140; <b>Build his home made antenna</b> is great ! You can find articles bellow :<br />
<blockquote><a href="http://makezine.com/projects/make-24/homemade-yagi-antenna/" target="_blank">Tune in to space with a homemade yagi antenna.</a>
	<br/>
	<a href="http://rksvyturys.blogspot.com/2013/08/compact-and-effective-2m70cm-dual-band.html" target="_blank">Compact and effective 2m/70cm dual band antenna for satellite operation</a>
	<br /><a href="Building the UHF Cheap Yagi for Satellite Communication" target="_blank">Building the UHF Cheap Yagi for Satellite Communication</a>
</blockquote>
<br/>
<h4>Station</h4><br/>

&#10140; Buy <b>motorized crossed yagi antennas</b> solution is the best solution but certainly not the cheaper ! Your choice can be a LEO-Pack Antenna System distributed by Amsat. <a href="http://store.amsat.org/catalog/product_info.php?products_id=123" target="_blank">Buy one</a>
<br /><br />&#10140; <b>Any vertical antenna</b> can be a solution but you can make QSO on most satellites, it is not really easy because you will fall on QSB but it can be a starting solution...
<br /><br />&#10140; <b>Built home made omnidirectional antennas</b> for satellites :
<blockquote>
This solution is perfect to automatically decode satellite signals but also to make QSOs on all satellites, easy to build and not expensive. You need pvc pipe and aluminum tube, few coaxials cables to do the phasing harness. You will have to build 4 antennas : 2 crossed Moxon for VHF and 2 crossed Moxon for UHF.<br/>If you use aluminium tube with diameter of 4mm you can fold easily the tubes. You can use electric clamp to make the connection.
<br />A is connected to first moxon to the copper core and connected to copper core of the first 75 &#8486; harness.
<br />B is connected to second moxon to the copper core and connected to copper  of the 50 &#8486; harness.
<br />C is connected to second moxon to the shield and connected to shield of the 50 &#8486; harness.
<br />D is connected to first moxon to the shield and connected to shield of the first 75 &#8486; harness.
<br />E is interconnection of to the reflectors.
<br/><br/>To help you with the dimensions, you can you the <a href="http://www.moxonantennaproject.com/construction.htm" target="_blank">Moxon construction software</a>.
My calculations for harness dimensions :<br />
For VHF : 2 X 1/4 75 &#8486; (TV cable with 0.84 velocity) : 433mm + 1 X 1/4 50 &#8486; (RG58) : 340mm.<br />
For UHF : 2 X 1/4 75 &#8486; (TV cable with 0.84 velocity) : 145mm + 1 X 1/4 50 &#8486; (RG58) : 114mm.<br />
Harness are placed ideally inside PVC tubes.
<br/>
<img src="/images/moxon-fg8oj.png">
<img width="827" height="827" src="/images/crossed-moxon.jpg">

<br/><br/>This adapted explanation is based on <a href="/images/VHFUHFSatelite.pdf" target="_blank">By L. B. Cebik, W4RNL article</a>.

</blockquote>

<?php 
echo '</td></tr></table>';

echo '</body></html>';
?>