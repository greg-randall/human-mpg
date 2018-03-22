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


    echo "<p>
At rest over 24 hours <a href=\"https://en.wikipedia.org/wiki/Basal_metabolic_rate#BMR_estimation_formulas\">your body uses</a> about " . round($bmr, 0) . " <a href=\"https://en.wikipedia.org/wiki/Food_energy\">calories</a>.
So every hour of the day you use about " . round($bmr / 24, 0) . " calories.
It takes <a href=\"https://en.wikipedia.org/wiki/Metabolic_equivalent#Epidemiology_and_public_health\">" . $mets . " times your basal metabolic rate</a> to walk at $speed MPH for an hour.
So every hour you walk you use about " . round(($bmr / 24) * $mets, 0) . " calories.
Which means that for every mile you walk you use about " . round((($bmr / 24) * $mets) / $speed, 0) . " calories.
For $diet_text <a href=\"https://blogs.scientificamerican.com/plugged-in/10-calories-in-1-calorie-out-the-energy-we-spend-on-food/\">it takes $fossilratio calories of fossil fuels to create every calorie you eat</a>.
So to walk a mile you use " . round(((($bmr / 24) * $mets) / $speed) * $fossilratio, 0) . " fossil fulel calories.
There are $gallonofgas calories in a gallon of gas.
So, <strong>your HumanMPG is: " . round($gallonofgas / (((($bmr / 24) * $mets) / $speed) * $fossilratio), 1) . "</strong> .</p>";



  //echo "<h3>".round($gallonofgas/(((($bmr/24)*$mets)/$speed)*$fossilratio),2)."</h3>";



    //record the input for debugging
    date_default_timezone_set("America/Chicago");
    $humanmpg = round($gallonofgas / (((($bmr / 24) * $mets) / $speed) * $fossilratio), 2);
    $output   = "$age,$height,$sex,$weight,$speed,$mets,$humanmpg," . get_client_ip() . "," . date("Y-m-d") . "," . date("H:i:s") . ",\r\n";
    file_put_contents("record.csv", $output, FILE_APPEND);
?>
<hr>
<p>
  There are a lot of assumptions made in this calcuator that probably make these numbers guesses:</p>
  <ul>
  <li>Is the basal metabloic rate calulation accurate?</li>
  <li>Why is the basal metabolic rate formula gendered?</li>
  <li>How accurate is the 10 (10:1) fossil fuel calories to food calories? How about 5 for vegetarians?</li>
	  <li>If you only take into account farming the ratio is 3:1. Processing, cooking, transport, packaging, etc make up the rest. https://www.mepartnership.org/counting-calories-in-agriculture/ </li>
  <li>How much energy is used making cars?</li>
  <li>Is the METS (Metabolic Equivalent) accurate?</li>
  <li>Does walking decrease the need for healthcare? How calorically valuable is health?</li>
   <li>If you live longer because you walk more is that better or worse emissions-wise for the world?</li>
  <li>Does a car produce more pollution per calorie than industrial food?</li>
  <li>Packaging contributes some fossil fuel calories to food, but it reduces spoilage. Is it better to have more packaging or less?</li>
  <li><a href="https://greentransportation.info/energy-transportation/gasoline-costs-6kwh.html">How much energy does converting crude oil to gasoline take?</a> 6kWh? 6kWh = 5159 calories; should we subtract that from our figure of <?php echo $gallonofgas; ?> calories per gallon of gas?</li>
</ul>

<?php echo file_get_contents('footer.html'); ?>
