<?php 
//CRON php /home/fg8ojcom/www/update_amsat.php 

include('/home/satfg8ojco/www/script_connect.php');


$url = 'http://www.amsat.org/status/';
$ct = file_get_contents($url) or die('Could not updated tle get file');
$ct=strtoupper($ct);
//echo strlen($ct);

$sql='SELECT ident,id FROM sat  ORDER BY RAND()';
$rs =mysql_query($sql);
while ($j=mysql_fetch_array($rs)) {
	$sat=strtoupper($j['ident']);
	$sat2=$sat;
	$sat2=str_replace('AO-07','[B]_AO-7',$sat2);
//$sat2=str_replace('ISS','ISS-FM',$sat2);
	echo "\n=======".$sat2."======= ";
	$satok=false;
	if (testsat($sat2)==true) {
		updatesat(1,$j['id']);
		echo " * ";
		$satok=true;
	}
	else {
		if ($sat2=='[B]_AO-7') {
			$sat2='[A]_AO-7';
				if (testsat($sat2)==true) {
					updatesat(1,$j['id']);
					echo " * ";
					$satok=true;
				}
			
		}
	}
	if ($satok==false) 	updatesat(0,$j['id']);
}

function updatesat($etat,$id) {
	$sql='SELECT etat FROM sat WHERE id='.$id;
	$rs=mysql_query($sql);
	$j=mysql_fetch_array($rs);
	$oid=$j[0];
	//if ($oid<>$id) {
		mysql_query('INSERT INTO sat_amsat (datetime,sat,etat) VALUES (NOW(),'.$id.','.$etat.');');
		mysql_query('UPDATE sat SET etat='.$etat.' WHERE id='.$id);	
	//}
}

function testsat($sat2) {
		global $ct;

		
		$ref='>'.$sat2.'</TD>';
		$ref2='>'.$sat2.'</A></TD>';
		$ref3="</TD>\n</TR>";
		$ref4="</TD></TR>";
		$tm='';
		if (strpos($ct,$ref)!==false) $tm=substr($ct,strpos($ct,$ref));
		if (strpos($ct,$ref2)!==false) $tm=substr($ct,strpos($ct,$ref2));
		if (strpos($tm,$ref3)!==false) $tm=substr($tm,0,strpos($tm,$ref3));
		if (strpos($tm,$ref4)!==false) $tm=substr($tm,0,strpos($tm,$ref4));
		$tml=explode("</TD>",$tm);
//print_r( $tml);

		$ok=999;
		$nook=999;

		for ($io = 1; $io <24; $io++) {
			if ( ($ok>$io) && (strpos($tml[$io],'4169E1')!==false) ) $ok=$io;
			if ( ($ok>$io) && (strpos($tml[$io],'9900FF')!==false) ) $ok=$io;
			if ( ($nook>$io) && (strpos($tml[$io],'"YELLOW"')!==false) ) $nook=$io;
			if ( ($nook>$io) && (strpos($tml[$io],'"RED"')!==false) ) $nook=$io;
		}
		if ($ok==0) $ok=999;
		if ($nook==0) $nook=999;
		
//echo "OK=".$ok."  NOOK=".$nook;
		if ($ok<$nook) {
			return true;
		}
		else {
			return false;
		}
}
?>