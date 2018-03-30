<?php
//data taken from https://en.wikipedia.org/wiki/Metabolic_equivalent#Epidemiology_and_public_health
//numbers between interpolated by using excel's forcast function

$metsmax=4;
$metsmin=1.7;

$metslookup = array(
"1.7" => 2.296,
"1.8" => 2.373,
"1.9" => 2.45,
"2.0" => 2.526,
"2.1" => 2.603,
"2.2" => 2.68,
"2.3" => 2.757,
"2.4" => 2.833,
"2.5" => 2.91,
"2.6" => 2.987,
"2.7" => 3.063,
"2.8" => 3.14,
"2.9" => 3.217,
"3.0" => 3.293,
"3.1" => 3.37,
"3.2" => 3.447,
"3.3" => 3.524,
"3.4" => 3.6,
"3.5" => 3.677,
"3.6" => 3.754,
"3.7" => 3.83,
"3.8" => 3.907,
"3.9" => 3.984,
"4.0" => 4.061);
?>
