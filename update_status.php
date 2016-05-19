<?php 

include('/home/satfg8ojco/www/script_connect.php');
date_default_timezone_set('GMT');
//data:"sat_id='.$sat_id.'="+SatCall+"&status="+st,

$sat_id=$_GET['sat_id'];
$SatCall=$_GET['SatCall'];
$status=$_GET['status'];

mysql_query('SET time_zone = "GMT";');
$sql='SELECT ident FROM sat WHERE id='.$sat_id;
$rs=mysql_query($sql);
$j=mysql_fetch_array($rs);
$sat=$j[0];
//$sat='AO-99';

$sql='SELECT id,TIME_TO_SEC(TIMEDIFF(NOW(),datetime)) FROM sat_status WHERE TIME_TO_SEC(TIMEDIFF(NOW(),datetime))<900 AND SatCall="'.$SatCall.'" AND sat_id='.$sat_id.' ORDER BY id desc LIMIT 1';
$rs=mysql_query($sql);
$sql='DELETE FROM sat_status WHERE TIME_TO_SEC(TIMEDIFF(NOW(),datetime))<900 AND SatCall="'.$SatCall.'" AND sat_id='.$sat_id;
mysql_query($sql);

if (mysql_num_rows($rs)==0) {
	//echo 'sat_id='+$sat_id.' SatCall='.$SatCall.' status='.$status;
	echo 'Thank you for taking the time to keep abreast of the satellite status !';
} else {

	echo 'Thank you to update the satellite status !';
}
$sql='INSERT INTO sat_status (SatCall,datetime,sat_id,status) VALUES ("'.$SatCall.'",NOW(),'.$sat_id.',"'.$status.'")';
mysql_query($sql);

$min=date('i');
if (substr($min,0,1)=='0') $min=substr($min,1);
if ($min<=60) $SatPeriod=3;
if ($min<=45) $SatPeriod=2;
if ($min<=30) $SatPeriod=1;
if ($min<=15) $SatPeriod=0;

//echo "min=".$min;
$sat=str_replace('LILACSAT-2','LilacSat-2',$sat);
$sat=str_replace('AO-07','[B]_AO-7',$sat);
$sat=str_replace('UKUBE-1','UKube-1',$sat);

$url= 'http://www.amsat.org/status/submit.php?SatSubmit=yes&Confirm=yes&SatName='.$sat.'&SatYear='.date('Y').'&SatMonth='.date('m').'&SatDay='.date('d').'&SatHour='.date('H').'&SatPeriod='.$SatPeriod.'&SatCall='.$SatCall.'&SatReport='.$status;

/*
echo "\n".$min."\n".$url;
exit();
*/
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL,$url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$return = curl_exec($curl);
curl_close($curl);

?>