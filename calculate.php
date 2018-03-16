<?php
include ("functions.php");//get_client_ip()

include ("metslookup.php");//mets table for lookup


//get passed variables:
$age = (int) $_GET["age"];

$height = inchestocm((((float) $_GET["height-f"])*12)+((float) $_GET["height-i"])); //feet *12 plus inches for height in inches

$sex = strtolower(substr(trim(preg_replace("/[^a-zA-Z]+/", "", (string) $_GET["sex"])),0,1));

$weight = lbstokg((float) $_GET["weight"]); //lbs

$speed = (float) $_GET["speed"]; //mph

//print passed variables
echo "<b>age:</b> ". $age ."<br>\r\n";
echo "<b>height: </b>";
echo displayfeetinches(cmtoinches($height)) ."<br>\r\n";
echo "<b>sex:</b> ". $sex ."<br>\r\n";
echo "<b>weight:</b> ". kgtolbs($weight) ." lbs<br>\r\n";
echo "<b>speed:</b> ". $speed ." mph<br>\r\n<br>\r\n<br>\r\n";



//constants
$fossilratio = 10;
$gallonofgas = 31520;


if($sex == "m"){//if sex is m use the male bmr calculation
	$bmr = 88.362 + (13.397 * $weight) + (4.799 * $height) - (5.677 * $age);
}else{//if sex is anything other than male use the female bmr calculation
	$bmr = 447.593 + (9.247 * $weight) + (3.098 * $height) - (4.330 * $age);
}

//make sure that the input speed is above the min and below the max in the mets table
if(($speed<=$metsmax)&&($speed>=$metsmin)){
	$mets = $metslookup[number_format($speed, 1)];//look up the mets based on the speed
}else {//if the speed is out of range use the middle speed
	$mets = 3.3; //middle speed & mets
	$speed = 3;
}

echo "<b>basal metabolic rate (bmr):</b> ". round($bmr,2) ."<br>\r\n";
echo "<b>mets multiplier:</b> ". $mets ."<br>\r\n<br>\r\n";
echo "<b>bmr/hour:</b> ". round($bmr/24,2) ."<br>\r\n";
echo "<b>cals/hour walking:</b> ". round(($bmr/24)*$mets,2) ."<br>\r\n";
echo "<b>cals/mile:</b> ". round((($bmr/24)*$mets)/$speed,2) ."<br>\r\n";
echo "<b>fossil cals/mile:</b> ". round(((($bmr/24)*$mets)/$speed)*$fossilratio,2) ."<br>\r\n";
$humanmpg = round($gallonofgas/(((($bmr/24)*$mets)/$speed)*$fossilratio),2);
echo "<h2>Human Mpg:</h2><br>\r\n";
echo "<h1>" . $humanmpg . "mpg</h1><br>\r\n";

date_default_timezone_set("America/Chicago");
$output = "$age,$height,$sex,$weight,$speed,$mets,$humanmpg," . get_client_ip() . "," . date("Y-m-d") . "," .date("H:i:s") . ",\r\n";
//echo $output;
file_put_contents("record.csv", $output, FILE_APPEND);

?>
