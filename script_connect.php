<?php
setlocale(LC_TIME, "fr_FR");

$db = new MyDB();
class MyDB extends SQLite3
{
    function __construct()
    {
        $this->open('/var/www/bdd/pitrack.sqlite');
    }
}
$db = new MyDB();

//$db = new SQLiteDatabase('/var/www/bdd/pitrack.sqlite');
 
function daynum2unix($dn) {
        // Converts a daynum to a UNIX timestamp

        return (86400.0 * ($dn - 2444238.5 + 3651.0));
}


function dateToCal($timestamp) {
  return date('Ymd\THis', $timestamp);
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

function bearing_dist($loc1, $loc2) {

	if (!valid_locator($loc1) || !valid_locator($loc2)) {
		return 0;
	}
		
	$l1 = loc_to_latlon($loc1);
	$l2 = loc_to_latlon($loc2);

	$co = cos($l1[1] - $l2[1]) * cos($l1[0]) * cos($l2[0]) +
			sin($l1[0]) * sin($l2[0]);
	$ca = atan2(sqrt(1 - $co*$co), $co);
	$az = atan2(sin($l2[1] - $l1[1]) * cos($l1[0]) * cos($l2[0]),
				sin($l2[0]) - sin($l1[0]) * cos($ca));

	if ($az < 0) {
		$az += 2 * M_PI;
	}

	$ret[km] = round(6371*$ca);
	$ret[deg] = round(rad_to_deg($az));

	return $ret;
}
function valid_locator ($loc) {
	if (ereg("^[A-R]{2}[0-9]{2}[A-X]{2}$", $loc)) {
		return 1;
	}
	else {
		return 0;
	}
}


function utfmod($x)
{
	$x=str_replace("Ãª","&ecirc;",$x);
	$x = str_replace("Ã©","&eacute;",$x);
	return $x;
}

function xml2array($xml) {
        $xmlary = array();
               
        $reels = '/<(\w+)\s*([^\/>]*)\s*(?:\/>|>(.*)<\/\s*\\1\s*>)/s';
        $reattrs = '/(\w+)=(?:"|\')([^"\']*)(:?"|\')/';

        preg_match_all($reels, $xml, $elements);

        foreach ($elements[1] as $ie => $xx) {
                $xmlary[$ie]["name"] = $elements[1][$ie];
               
                if ($attributes = trim($elements[2][$ie])) {
                        preg_match_all($reattrs, $attributes, $att);
                        foreach ($att[1] as $ia => $xx)
                                $xmlary[$ie]["attributes"][$att[1][$ia]] = $att[2][$ia];
                }

                $cdend = strpos($elements[3][$ie], "<");
                if ($cdend > 0) {
                        $xmlary[$ie]["text"] = substr($elements[3][$ie], 0, $cdend - 1);
                }

                if (preg_match($reels, $elements[3][$ie]))
                        $xmlary[$ie]["elements"] = xml2array($elements[3][$ie]);
                else if ($elements[3][$ie]) {
                        $xmlary[$ie]["text"] = $elements[3][$ie];
                }
        }

        return $xmlary;
}

function getarray($resarray)
{

		foreach ($resarray as $resname => $resval) {
			$valarray[$resname] = sxml2Array($resval);
		}
  
	return $valarray;
}

//== protected functions =============================================================

/**
* returns array with values of given simpleXml objekt
* @return array
*/
function sxml2Array(SimpleXMLElement $sxmlo)
{
	$values = ((array) $sxmlo);
	foreach ($values as $index => $value) {
		if (!is_string($value)) {
			$values[$index] = sxml2Array($value);
		}
		else {
			$values[$index] = $value;
		}
	}
	
	return $values;
}

 function getElement($dom_element, $object_element) {
	
    // récupération du nom de l'élément
    $object_element->name = $dom_element->nodeName;
	
    // récupération de la valeur CDATA, 
    // en supprimant les espaces de formatage.
    $object_element->textValue = trim($dom_element->firstChild->nodeValue);
	
    // Récupération des attributs
    if ($dom_element->hasAttributes()) {
      $object_element->attributes = array();
        foreach($dom_element->attributes as $attName=>$dom_attribute) {
          $object_element->attributes[$attName] = $dom_attribute->value;
        }
    }
	
    // Récupération des éléments fils, et parcours de l'arbre XML
    // on veut length >1 parce que le premier fils est toujours 
    // le noeud texte
    if ($dom_element->childNodes->length > 1) {
      $object_element->children = array();
      foreach($dom_element->childNodes as $dom_child) {
        if ($dom_child->nodeType == XML_ELEMENT_NODE) {
          $child_object = new stdClass();
          getElement($dom_child, $child_object);
          array_push($object_element->children, $child_object);
        }
      }
    }
  }


  
function jour($x)
{
	if ($x=="lun.") $x="lundi";
	if ($x=="mar.") $x="mardi";
	if ($x=="mer.") $x="mercredi";
	if ($x=="jeu.") $x="jeudi";
	if ($x=="ven.") $x="vendredi";
	if ($x=="sam.") $x="samedi";
	if ($x=="dim.") $x="dimanche";
	return $x;
}



function urlenc($tu)
{
	$tu=strtolower($tu);
	$tu = str_replace("\n","",$tu);
	$tu = str_replace("\r","",$tu);
	//echo $tu;
	$tu = str_replace("&#39;","+",$tu);
	$tu = str_replace("&#233;","e",$tu);
	$tu = str_replace("&#039;","+",$tu);
	$tu = str_replace("&#8211;","+",$tu);
	$tu = str_replace("&#8217;","+",$tu);
	$tu = str_replace("#","+",$tu);
	
	for ($i=0;$i<5;$i++)
	{
		$pos= strpos($tu,"&#");
		if ($pos!==false)
		{
			$t = substr($t,0,$t);
			if (strpos($t,";")<6)
			{
				$tu=substr($tu,0,$pos) . substr($tu,$pos+$t);
			}
		}
	}

	$tu = str_replace(" ","+",$tu);
	$tu = str_replace("%e2","a",$tu);
	$tu = str_replace("apos","+",$tu);
	
	$tu = str_replace(";","+",$tu);

	$tu = str_replace("/","+",$tu);
	
	$tu = str_replace("\\","+",$tu);
		
	$tu = str_replace("«","+",$tu);

	$tu = str_replace("’","+",$tu);
	$tu = str_replace("	","+",$tu);
	$tu = str_replace("-","+",$tu);
	$tu = str_replace(" ","+",$tu);
	$tu = str_replace("…","+",$tu);
	$tu = str_replace("´","+",$tu);
	$tu = str_replace("¸","+",$tu);
	$tu = str_replace("î","i",$tu);
	$tu = str_replace("ï","i",$tu);
	$tu = str_replace("°","+",$tu);
	$tu = str_replace("%","+",$tu);
	$tu = str_replace("'","+",$tu);
	$tu = str_replace("%27","+",$tu);
	$tu = str_replace("#8217","+",$tu);
	$tu = str_replace("%bb","+",$tu);
	$tu = str_replace("%ab","+",$tu);
	$tu = str_replace("%238217","+",$tu);
	$tu = str_replace("%3b","+",$tu);
	$tu = str_replace("%2f","+",$tu);
	$tu = str_replace("%2b","+",$tu);
	$tu = str_replace("«","+",$tu);
	$tu = str_replace("»","+",$tu);
	$tu = str_replace("%28","+",$tu);
	$tu = str_replace("%29","+",$tu);
	$tu = str_replace("%20","+",$tu);
	$tu = str_replace("%f4","o",$tu);
	$tu = str_replace("%e0","a",$tu);
	$tu = str_replace("%e7","c",$tu);
	$tu = str_replace("%e9","e",$tu);
	$tu = str_replace("%25","+",$tu);
	$tu = str_replace("%ea","e",$tu);
	$tu = str_replace("%c3","e",$tu);
	$tu = str_replace("%3a","+",$tu);
	$tu = str_replace("%26quot%3b","+",$tu);
	$tu = str_replace("%26","+",$tu);
	$tu = str_replace("%22","+",$tu);
	$tu = str_replace("%e8","e",$tu);
	$tu = str_replace("%2c","+",$tu);		
	$tu = str_replace("%26%238217%3b","+",$tu);
	$tu = str_replace("%ee","i",$tu);
	
	$tu = str_replace("!","+",$tu);
	$tu = str_replace("\"","+",$tu);
	$tu = str_replace("'","+",$tu);
	$tu = str_replace(":","+",$tu);
	$tu = str_replace("-","+",$tu);
	$tu = str_replace(".","+",$tu);
	$tu = str_replace("'","+",$tu);	
	$tu = str_replace("(","+",$tu);	
	$tu = str_replace(")","+",$tu);	
	$tu = str_replace("é","e",$tu);	
	$tu = str_replace("è","e",$tu);
	$tu = str_replace("ê","e",$tu);
	$tu = str_replace("è","e",$tu);
	$tu = str_replace("à","a",$tu);
	$tu = str_replace("²","2",$tu);
	$tu = str_replace(",","+",$tu);
	$tu = str_replace("ç","c",$tu);
	$tu = str_replace("]","+",$tu);
	$tu = str_replace("[","+",$tu);
	
		
	
	
	$tu = str_replace("&euro;","e",$tu);
	$tu = str_replace("?","+",$tu);
	$tu = str_replace("&","+",$tu);
	$tu = str_replace(",","+",$tu);
	$tu=str_replace("ö","o",$tu);
	$tu = str_replace("ô","o",$tu);
	$tu = str_replace("ù","u",$tu);
	$tu = str_replace("ü","u",$tu);
	$tu = str_replace("û","u",$tu);
	$tu = str_replace("â","a",$tu);
	$tu = str_replace("ë","e",$tu);
	$tu = str_replace("é","e",$tu);
	$tu = str_replace("è","e",$tu);
	$tu = str_replace("ê","e",$tu);
	
	$tu = str_replace("ë","e",$tu);
	$tu = str_replace("%eb","e",$tu);
	//echo $tu;
	$tu = str_replace("'","+",$tu);
	
	$tu = str_replace("€","e",$tu);
	$tu = str_replace("&amp;euro;","e",$tu);
	$tu = str_replace("&euro;","e",$tu);
	$tu = str_replace("+20","+",$tu);
		
		for ($i=0;$i<5;$i++)
		{
			if (substr($tu,strlen($tu)-1)=="+") $tu = substr($tu,0,strlen($tu)-1);
			if (substr($tu,0,1)=="+") $tu = substr($tu,1,strlen($tu));
			$tu = str_replace("++","+",$tu);
		}
		
		$tu=trim($tu);
		//$tu = str_replace("+","-",$tu);
		return $tu;
}

function randomPassword($nb) {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    for ($i = 0; $i < $nb; $i++) {
        $n = rand(0, 49);
        $pass .= $alphabet[$n];
    }
    return $pass;
}

?>