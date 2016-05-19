<?php 
$debug=false;
$id=$_GET['id'];
$path='/home/satfg8ojco/tmp/';
$file=$path.$id.'.img';


$draw = new \ImagickDraw();
$draw2 = new \ImagickDraw();
$strokeColor = new \ImagickPixel('rgb(255, 255, 255)');
$fillColor = new \ImagickPixel('DodgerBlue2');
$fillColor2 = new \ImagickPixel('red');
$fillColor3 = new \ImagickPixel('black');
$fillColor4 = new \ImagickPixel('gray');
$draw->setFillColor($fillColor);
$draw->circle(160,160,250,250);
$draw->setFillColor($strokeColor);
$draw->circle(160,160,249,249);
$draw->setFillColor($fillColor);
$draw->line(160,25,160,285);
$draw->line(32,160,285,160);
$draw->setFontSize(18);
$draw->annotation(154,22,'N');
$draw->annotation(295,165,'E');
$draw->annotation(2,165,'W');
$draw->annotation(154,308,'S');
$draw->setFillColor($fillColor2);
if (file_exists($file)==true) {
	$ct = file_get_contents($file) or die('Could not updated tle get file');
	$l=explode("\n",$ct);

	for ($i=0;$i<count($l)-1;$i++) {
		$val=explode(',',$l[$i]);
		$ele=round($val[2]*128/90);
		$azi=$val[1];
		$azi=($azi-90);
		$azi=deg2rad($azi);
		//if ($azi<0) $azi=($azi+360);
		$azix=cos($azi);
		$azix=(int)($azix*(128-$ele));
		$aziy=sin($azi);
		$aziy=(int)($aziy*(128-$ele));
if ($debug==true) echo "<br>\nheure=".$val[0].' azim='.$val[1].' ele='.$val[2] . '/'.$ele;	
if ($debug==true) echo ' azi='.round($val[1],0).' azix='.$azix.' aziy='. $aziy;
		$x=$azix+160;
		$y=$aziy+160;
		if (strlen($val[0])>0) {
			if (($val[0]=='AOS')||($val[0]=='LOS')) {
				$draw2->setFontSize(20);
				$draw2->setStrokeAntialias(true);
				$draw2->setFillColor($fillColor3);
				$dx=-50;
				$dy=0;
				if ($x<160) $dx=+20;
				if ($y<160) $dy=+20;
				$draw2->annotation($x+$dx,$y+$dy,$val[0]);
				$draw2->circle($x-2,$y-2,$x+2,$y+2);	
			}
			else {
				$draw2->setStrokeAntialias(true);
				$draw2->setFontSize(14);
				$draw2->setFillColor($fillColor3);
				
				$draw2->annotation($x-20,$y+5,$val[0]);
			}
			
		}
		$draw->setFillColor($fillColor2);
		$draw->circle($x,$y,$x+2,$y+2);	
		
		
//if ($i==0) break;		
	}
}
$imagick = new \Imagick();
$imagick->newImage(320, 320, $strokeColor);
$imagick->setImageFormat("png");
$imagick->drawImage($draw);
$imagick->drawImage($draw2);
if ($debug==false) { 
	header("Content-Type: image/png");
	echo $imagick->getImageBlob();
}    



$f=scandir($path,0);
for ($i=0;$i<count($f);$i++) {
		$t=$f[$i];
		if (substr($t,strlen($t)-4)=='.img') {
		$date = new DateTime();
		if ($debug==true) echo '<bR>'. $t .'/'.($date->getTimestamp()-filectime($path.$t));
			if (($id<>substr($t,0,strlen($t)-4)) &&($date->getTimestamp()-filectime($path.$t)>300)) unlink($path.$t);
		}
}


 ?>