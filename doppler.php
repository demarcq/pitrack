<?php


function solve()
{
//    if(!$_GET['submit']) 
  //      return null;
  //  if(getMissingVar() == "Error")
    //    return "Error: Too many undefined variables or invalid inputted variables";
    $s = $_GET['s'];
    $v = $_GET['v'];
    $f1 = $_GET['f1'];
    $f2 = $_GET['f2'];
    $dir = $_GET['dir'];
   if ($dir == "away") {
	        $o= ($f1 * ($s + $v))/$s; 
	} else {
	        $o= ($f1 * ($s - $v))/$s;
	}
    
    return $o;
}
?>


<form name="form" method="GET">
    <b>Speed of sound in m/s</b><br />
    <input type="text" name="s" value="299792458"/><br/>
    <b>Speed of object in m/s</b><br />
    <input type="text" name="v" value="7.5347723873092"><br/>
    <b>Emitting Frequency in Hz</b><br />
    <input type="text" name="f1" value="436800000"><br/>
    <b>Perceived Frequency</b><br />
    <input type="text" name="f2" value=""><br/>
    <b>Direction</b><br />
    <input type="radio" checked name="dir" value="to" required/>Towards you<br />
    <input type="radio" name="dir" value="away" required/>Away from you<br />
    <input type="submit" value="Calculate" name="submit"/><br/>
</form>

<h3>Perceived Frequency:</h3>
<h4>
<?php
    if($_GET['submit'])
    echo solve();
?>
</h4>
</body>
</html>