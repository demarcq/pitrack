<?php
include('/home/satfg8ojco/www/script_connect.php');

error_reporting(E_ALL);
$url = 'http://www.amsat.org/amsat/ftp/keps/current/nasa.all';
$contentsOriginal = file_get_contents($url) or die('Could not updated tle get file');
$contentsOriginal=explode("\n",$contentsOriginal);

$sql='SELECT ident FROM sat ORDER BY RAND()';
$rs=mysql_query($sql);
while ($j=mysql_fetch_array($rs)) {
	$sat=$j['ident'];
	if ($sat=='ISS-DATA') $sat='ISS';
	if ($sat=='ISS-FM') $sat='ISS';
	for($i=0;$i<count($contentsOriginal);$i++) {
		if (strpos($contentsOriginal[$i],$sat)!==false) {
			$contents = $contentsOriginal[$i]."\r\n".$contentsOriginal[$i+1]."\r\n".$contentsOriginal[$i+2]."\r\n";
		}
	}
	$sql='UPDATE sat SET tle="'.$contents.'" WHERE ident="'.$j['ident'].'"';
	mysql_query($sql);
	echo $sql;

}
?>