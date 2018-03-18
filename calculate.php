<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
	<title>HumanMPG</title>
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="custom.css" rel="stylesheet">
</head>
<body>
	<div class="jumbotron jumbotron-fluid">
		<div class="container">
			<h1 class="display-3">HumanMPG</h1>
			<p class="lead">Ever wondered how much gasoline it takes you to walk a mile?</p>
		</div>
	</div>
	<div class="container">

<?php
include ("functions.php");

include ("metslookup.php");//mets table for lookup

include ("default-values.php");//defaults for values


//get passed variables & validate:
if(is_numeric($_GET["age"])){
  $age = (int) $_GET["age"];
}else{
	$age=$d_age;
}
if($age<0){
	$age=$d_age;
}

if(is_numeric($_GET["height-f"]) | is_numeric($_GET["height-i"])){
  $height = inchestocm((((float) $_GET["height-f"])*12)+((float) $_GET["height-i"])); //feet *12 plus inches for height in inches
}else{
	$height=$d_height;
}
if($height<0){
	$height=$d_height;
}





if(is_string($_GET["sex"])){
  $sex = strtolower(trim((string) $_GET["sex"]));
}else{
	$sex=$d_sex;
}
if($sex!="m" && $sex!="f"){
	$sex=$d_sex;
}


if(is_numeric($_GET["weight"])){
  $weight = lbstokg((float) $_GET["weight"]); //lbs
}else{
	$weight=$d_weight;
}
if($weight<0){
	$weight=$d_weight;
}

if(is_numeric($_GET["speed"])){
  $speed = (float) $_GET["speed"]; //mph
}else{
	$speed=$d_speed;
}
if(($speed>$metsmax)|($speed<$metsmin)){
  $speed=$d_speed;
}


if(is_string($_GET["diet"])){
  $diet = strtolower(trim( (string) $_GET["diet"] ));
}else{
	$diet=$d_diet;
}
if($diet!="o" && $diet!="v"){
	$diet=$d_diet;
}


//echo "information you entered:<Br>\r\n";
//echo "<b>Age:</b> ". $age ."<br>\r\n";
//echo "<b>Height: </b>";
//echo displayfeetinches(cmtoinches($height)) ."<br>\r\n";
//echo "<b>Sex:</b> ". $sex ."<br>\r\n";
//echo "<b>Weight:</b> ". kgtolbs($weight) ." lbs<br>\r\n";
//echo "<b>Speed:</b> ". $speed ." mph<br>\r\n";
//echo "<b>Diet:</b> ". $diet ."<br>\r\n<br>\r\n<br>\r\n";



if($diet=="o"){//if the person is an ominivore use 10x, if they're a vegetarian, use 5x. if something weird happened use 10x
  $fossilratio = 10;
}else if($diet=="v"){
	$fossilratio = 5;
}else{
	$fossilratio = 10;
}

$gallonofgas = 31520;//calories in gallon of gas
//echo "Calories in a gallon of gas: ".$gallonofgas ."<br><br><br>\r\n";

/* https://en.wikipedia.org/wiki/Harris%E2%80%93Benedict_equation
M BMR = (10 * weight in kg) + (6.25 * height in cm) - (5 * age in years) + 5
W	BMR = (10 * weight in kg) + (6.25 * height in cm) - (5 * age in years) - 161 */
if($sex == "m"){//if sex is m use the male bmr calculation
	$bmr = (10 * $weight ) + (6.25 * $height) - (5 * $age) + 5;
}else{//if sex is anything other than male use the female bmr calculation
	$bmr = (10 * $weight ) + (6.25 * $height) - (5 * $age) - 161;
}

$mets = $metslookup[number_format($speed, 1)];//look up the mets based on the speed


if($fossilratio ==10){
  $diet_text="omnivores";
}else{
  $diet_text="vegetarians";
}

echo "<p>At rest over 24 hours <a href=\"https://en.wikipedia.org/wiki/Basal_metabolic_rate#BMR_estimation_formulas\">your body uses</a> about ".round($bmr,0)." <a href=\"https://en.wikipedia.org/wiki/Food_energy\">calories</a>.
So every hour of the day you use about ". round($bmr/24,0) ." calories.
It takes ".$mets." times your base calories to walk at $speed MPH for an hour.
So every hour you walk you use about ".round(($bmr/24)*$mets,0) ." calories.
Which means that for every mile you walk you use about ".round((($bmr/24)*$mets)/$speed,0)." calories.
For $diet_text it takes $fossilratio calories of fossil fuels to create every calorie you eat.
So to walk a mile you use ".round(((($bmr/24)*$mets)/$speed)*$fossilratio,0)." fossil fulel calories.
There are $gallonofgas calories in a gallon of gas.
So, your miles per gallon of gas is ".round($gallonofgas/(((($bmr/24)*$mets)/$speed)*$fossilratio),1).".";

//echo "<b>Basal Metabolic Rate (bmr):</b> ". round($bmr,2) ."<br>\r\n";
//echo "<b>METS Multiplier (based on walking speed):</b> ". $mets ."<br>\r\n<br>\r\n";
//echo "<b>bmr/hour (ie calories per hour):</b> ". round($bmr/24,2) ."<br>\r\n";
//echo "<b>cals/hour walking (bmr/hour * mets):</b> ". round(($bmr/24)*$mets,2) ."<br>\r\n";
//echo "<b>cals/mile:</b> ". round((($bmr/24)*$mets)/$speed,2) ."<br>\r\n";
//echo "<b>fossil cals/mile:</b> ". round(((($bmr/24)*$mets)/$speed)*$fossilratio,2) ."<br>\r\n";
//$humanmpg = round($gallonofgas/(((($bmr/24)*$mets)/$speed)*$fossilratio),2);
//echo "<h2>Human Mpg:</h2><br>\r\n";
//echo "<h1>" . $humanmpg . "mpg</h1><br>\r\n";


//record the input for debugging
date_default_timezone_set("America/Chicago");
$humanmpg = round($gallonofgas/(((($bmr/24)*$mets)/$speed)*$fossilratio),2);
$output = "$age,$height,$sex,$weight,$speed,$mets,$humanmpg," . get_client_ip() . "," . date("Y-m-d") . "," .date("H:i:s") . ",\r\n";
file_put_contents("record.csv", $output, FILE_APPEND);
?>



</div>
<script src="js/bootstrap.min.js">
</script>
<script src="js/jquery.js">
</script>
</body>
</html>
