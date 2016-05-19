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

<h3>Starting on Amateur Radio Satellite is really easy !</h3>
<b>Really ? What do I need ?</b><br/>
<br />&#10140; First, you need a amateur radio license ! If not, you will be abble to listen but not to transmit ! If you want a licence, you have to contact a Radio Club near you to prepare the exam ! In US, take a look at <a href="http://www.arrl.org/ham-radio-licenses" target="_blank">ARRL website</a> !<br/>
<br />&#10140; You need a VHF/UHF radio : It can be a FM handset or a SSB transceiver. You can also receive with a SDR dongle like the Funcube USB dongle.
<br /><br />&#10140; You need a antenna, for radio, it is recommended :) <a href="/antenna.php">You will find a specific page about this subject here</a>.
<br /><br />&#10140; You have to know which satellite on which frequency and at what time you can catch him ! It can be tiresome to hold  your antenna in one hand during hours but you don't have to do this ! This website can give you all this informations, you only have to know where you are and to translate this in QTH locator (<a href="http://qthlocator.free.fr/" target="_blank">a 6 caracters long position on all the world that you can find on this website</a>).
<br /><br />&#10140; What else ? That's almost all. You have a lean how to play with doppler effect which modify frequencies and how to make contact but it is like each time with amateurs radio stations : Listen before to call ! Listening is learning  !

<?php 
echo '</td></tr></table>';

echo '</body></html>';
?>