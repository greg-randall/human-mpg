<?php echo file_get_contents('header.html'); ?>


<?php
    include("functions.php");
    include("metslookup.php"); //mets table for lookup
    include("default-values.php"); //defaults for values

    //get passed variables & validate:

    //validate Age
    if (is_numeric($_GET["age"])) {
      $age = (int) $_GET["age"];
    } else {
      $age = $d_age;
    }
    if ($age < 0) {
      $age = $d_age;
    }

    //validate & convert height to cm
    if (is_numeric($_GET["height-f"]) | is_numeric($_GET["height-i"])) {
      $height = inchestocm((((float) $_GET["height-f"]) * 12) + ((float) $_GET["height-i"])); //feet *12 plus inches for height in inches
    } else {
      $height = $d_height;
    }
    if ($height < 0) {
      $height = $d_height;
    }

    //validate sex
    if (is_string($_GET["sex"])) {
      $sex = strtolower(trim((string) $_GET["sex"]));
    } else {
      $sex = $d_sex;
    }
    if ($sex != "m" && $sex != "f") {
      $sex = $d_sex;
    }

    //validate weight & convert to KG
    if (is_numeric($_GET["weight"])) {
      $weight = lbstokg((float) $_GET["weight"]); //lbs
    } else {
      $weight = $d_weight;
    }
    if ($weight < 0) {
      $weight = $d_weight;
    }

    //validate speed & verifiy speed is not outside the min/max speed for the METS table
    if (is_numeric($_GET["speed"])) {
      $speed = (float) $_GET["speed"]; //mph
    } else {
      $speed = $d_speed;
    }
    if (($speed > $metsmax) | ($speed < $metsmin)) {
      $speed = $d_speed;
    }

    //validate diet type
    if (is_string($_GET["diet"])) {
      $diet = strtolower(trim((string) $_GET["diet"]));
    } else {
      $diet = $d_diet;
    }
    if ($diet != "o" && $diet != "v") {
      $diet = $d_diet;
    }
    //if they're a vegetarian, use 5x. else assume omnivore and use 10x,
    if($diet == "v") {
      $fossilratio = 5;
      $diet_text = "vegetarians";
    }else{
      $fossilratio = 10;
      $diet_text = "omnivores";
    }


    // https://en.wikipedia.org/wiki/Harris%E2%80%93Benedict_equation
    // M BMR = (10 * weight in kg) + (6.25 * height in cm) - (5 * age in years) + 5
    // W BMR = (10 * weight in kg) + (6.25 * height in cm) - (5 * age in years) - 161
    if ($sex == "m") { //if sex is m use the male bmr calculation
      $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age) + 5;
    } else { //if sex is anything other than male use the female bmr calculation
      $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age) + -161;
    }

    $mets = $metslookup[number_format($speed, 1)]; //look up the mets based on the speed

echo "<h3>You get " . round($gallonofgas / (((($bmr / 24) * $mets) / $speed) * $fossilratio), 1) . " miles per gallon of gas!</h3><br>";
    echo "<p>
At rest over 24 hours <a href=\"https://en.wikipedia.org/wiki/Basal_metabolic_rate#BMR_estimation_formulas\">your body uses</a> about " . round($bmr, 0) . " <a href=\"https://en.wikipedia.org/wiki/Food_energy\">calories</a>.
Every hour of the day you use about " . round($bmr / 24, 0) . " calories.
It takes <a href=\"https://en.wikipedia.org/wiki/Metabolic_equivalent#Epidemiology_and_public_health\">" . round($mets,1) . " times your resting calorie rate</a> to walk at ".$speed."MPH;
so, every hour you walk you use about " . round(($bmr / 24) * $mets, 0) . " calories.
Which means that for every mile you walk you use about " . round((($bmr / 24) * $mets) / $speed, 0) . " calories.
For $diet_text <a href=\"https://blogs.scientificamerican.com/plugged-in/10-calories-in-1-calorie-out-the-energy-we-spend-on-food/\">it takes $fossilratio calories of fossil fuels to create every calorie you eat</a>.
So to walk a mile you use " . round(((($bmr / 24) * $mets) / $speed) * $fossilratio, 0) . " fossil fuel calories.
There are $gallonofgas calories in a gallon of gas.
So, <strong>your HumanMPG is: " . round($gallonofgas / (((($bmr / 24) * $mets) / $speed) * $fossilratio), 1) . "</strong>.</p>";



    //record the input for debugging
    date_default_timezone_set("America/Chicago");
    $humanmpg = round($gallonofgas / (((($bmr / 24) * $mets) / $speed) * $fossilratio), 2);
    $output   = "$age,$height,$sex,$weight,$speed,$mets,$humanmpg," . get_client_ip() . "," . date("Y-m-d") . "," . date("H:i:s") . ",\r\n";
    file_put_contents("record.csv", $output, FILE_APPEND);

?>
<div class="row justify-content-center" ><div class="col-xs-12"><a class="btn btn-info" href="http://humanmpg.com/" role="button">Find the HumanMPG for someone else!</a></div></div>
<hr style="margin-top:25px;margin-bottom:15px;">
<p>
  There are a lot of assumptions made in this calculator that probably make these numbers guesses:</p>
  <ul>
  <li>How accurate is the 10 (10:1) fossil fuel calories to food calories? How about 5 for vegetarians?</li>
	<li><a href="https://www.mepartnership.org/counting-calories-in-agriculture/">If you only take into account farming the ratio is 3:1</a>. Processing, cooking, transport, packaging, etc make up the rest. How should we account for people who eat packaged meals vs freshly cooked food?</li>
  <li>How much energy is used making cars? How much energy is that per mile of the life of the car?</li>
  <li>Is <a href="https://onlinelibrary.wiley.com/doi/pdf/10.1002/clc.4960130809">Metabolic Equivalent</a> accurate?</li>
  <li>Is the <a href="https://en.wikipedia.org/wiki/Harris%E2%80%93Benedict_equation">basal metabolic rate (BMR) calculation</a> accurate? Should it be gendered? What age and weight ranges is the BMR formula valid for?</li>
  <li>Does walking decrease the need for healthcare? How calorically valuable is health?</li>
  <li>If you live longer because you walk more is that better or worse emissions-wise for the world?</li>
  <li>Is a car's consumption of a calorie more or less polluting than than a calorie produced for people?</li>
  <li>Packaging contributes some fossil fuel calories to food, but it reduces spoilage. Is it better to have more packaging or less?</li>
  <li><a href="https://greentransportation.info/energy-transportation/gasoline-costs-6kwh.html">How much energy does converting crude oil to gasoline take?</a> 6kWh? 6kWh = 5159 calories; should we subtract that from our figure of <?php echo $gallonofgas; ?> calories per gallon of gas?</li>
</ul>

<?php echo file_get_contents('footer.html'); ?>
