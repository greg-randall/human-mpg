<?php
include ("functions.php");//get_client_ip()

include ("metslookup.php");//mets table for lookup

include ("default-values.php");//defaults for values

//get passed variables & validate:
if($_GET["age"] && $_GET["age"]>1 && $_GET["age"]<120){
  $age = (int) $_GET["age"];
}else{
	$age=$d_age;
}

if($_GET["height-f"] | $_GET["height-i"]){
  $height = inchestocm((((float) $_GET["height-f"])*12)+((float) $_GET["height-i"])); //feet *12 plus inches for height in inches
}else{
	$height=$d_height;
}

if($height<121 | $height>213) { // if height is less than 4 feet or greater than 7 feet
	$height=$d_height;
}

if($_GET["sex"]){
  $sex = strtolower(trim((string) $_GET["sex"]));
}else{
	$sex=$d_sex;
}

if($sex!="m" && $sex!="f"){

	$sex=$d_sex;
}


if($_GET["weight"]){
  $weight = lbstokg((float) $_GET["weight"]); //lbs
}else{
	$weight=$d_weight;
}

if($_GET["speed"]){
  $speed = (float) $_GET["speed"]; //mph
}else{
	$speed=$d_speed;
}


if($_GET["diet"]){
  $diet = strtolower(trim( (string) $_GET["diet"] ));
}else{
	$diet=$d_diet;
}
if($diet!="o" && $diet!="v"){
	$diet=$d_diet;
}


//print passed variables
echo "<b>Age:</b> ". $age ."<br>\r\n";
echo "<b>Height: </b>";
echo displayfeetinches(cmtoinches($height)) ."<br>\r\n";
echo "<b>Sex:</b> ". $sex ."<br>\r\n";
echo "<b>Weight:</b> ". kgtolbs($weight) ." lbs<br>\r\n";
echo "<b>Speed:</b> ". $speed ." mph<br>\r\n";
echo "<b>Diet:</b> ". $diet ."<br>\r\n<br>\r\n<br>\r\n";



if($diet=="o"){
		$fossilratio = 10;
}else if($diet=="v"){
	$fossilratio = 5;
}


$gallonofgas = 31520;//calories in gallon of gas


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

echo "<b>Basal Metabolic Rate (bmr):</b> ". round($bmr,2) ."<br>\r\n";
echo "<b>METS Multiplier:</b> ". $mets ."<br>\r\n<br>\r\n";
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
