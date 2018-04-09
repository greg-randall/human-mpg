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

$humanmpg = round($gallonofgas / (((($bmr / 24) * $mets) / $speed) * $fossilratio), 1);

echo "<h3>You get " . $humanmpg . " miles per gallon of gas!</h3><br>";
    echo "<p>
At rest over 24 hours, <a href=\"https://en.wikipedia.org/wiki/Basal_metabolic_rate#BMR_estimation_formulas\">your body uses</a> about " . number_format($bmr) . " <a href=\"https://en.wikipedia.org/wiki/Food_energy\">calories</a>.
Every hour of the day you use about " . number_format($bmr / 24) . " calories.
It takes <a href=\"https://en.wikipedia.org/wiki/Metabolic_equivalent#Epidemiology_and_public_health\">" . round($mets,1) . " times your resting calorie rate</a> to walk at ".$speed."MPH;
so, every hour you walk you use about " . round(($bmr / 24) * $mets, 0) . " calories.
Which means that for every mile you walk you use about " . number_format((($bmr / 24) * $mets) / $speed) . " calories.
For $diet_text, <a href=\"https://blogs.scientificamerican.com/plugged-in/10-calories-in-1-calorie-out-the-energy-we-spend-on-food/\">it takes $fossilratio calories of fossil fuel to create every calorie you eat</a>.
So to walk a mile you use " . round(((($bmr / 24) * $mets) / $speed) * $fossilratio, 0) . " fossil fuel calories.
There are ".number_format($gallonofgas)." calories in a gallon of gas.
So, <strong>your HumanMPG is " . $humanmpg . "</strong>.</p>";


if($humanmpg>$fuel_effiecnty_2016){
  $better_worse = "better";
  $drive_walk = "walk";
}else{
    $better_worse = "worse";
    $drive_walk = "drive";
}
echo "<p>Your MPG is $better_worse than the <a href=\"https://www.reuters.com/article/us-autos-emissions/u-s-vehicle-fuel-economy-rises-to-record-24-7-mpg-epa-idUSKBN1F02BX\">average car from 2016 which gets ".$fuel_effiecnty_2016."MPG</a>; so <strong>it's probably better for the environment for you to $drive_walk</strong>.</p>";


    //record the input for debugging
    date_default_timezone_set("America/Chicago");
    $output   = date("Y-m-d") . "," . date("H:i:s") . ",$age,$height,$sex,$weight,$speed,$mets,$humanmpg," . get_client_ip() . "\r\n";
    if(!file_exists("record.csv")){
      file_put_contents("record.csv", "date,time,age,height-cm,sex,weight-kg,speed-mph,mets,humanmpg\r\n", FILE_APPEND);
    }
    file_put_contents("record.csv", $output, FILE_APPEND);

?>
<div class="row justify-content-center" ><div class="col-xs-12"><a class="btn btn-info" href="http://humanmpg.com/" role="button">Find the HumanMPG for someone else!</a></div></div>

<hr style="margin-top:25px;margin-bottom:15px;">
<p>With this calculator, we're ignoring most of the fossil fuel expenditure involved in making a car, refining fuel, etc. For an average car including these total cost of ownership numbers means that cars use about 20% more fossil fuel per mile than you would otherwise estimate using miles per gallon:
<ul>
  <li><a href="https://greet.es.anl.gov/files/vehicle_and_components_manufacturing">A car takes about 8,126,195 calories to build</a>. <a href="https://en.wikipedia.org/wiki/Car_longevity#Statistics">Modern cars last about 200,000 miles</a>. 8,126,195calories / 200,000miles = <strong>41 calories</strong>.</li>
  <li><a href="https://greentransportation.info/energy-transportation/gasoline-costs-6kwh.html">It's estimated to take 6kWh (<?php echo round(kwhtokcal(6),0); ?> calories) to refine crude oil into a gallon of gasoline</a>. That means for every mile an average car uses an additional <?php echo round(kwhtokcal(6),0); ?> calories / <?php echo $fuel_effiecnty_2016; ?> miles per gallon = <strong>209 calories</strong>.</a></li>
  <li><a href="http://ridetowork.org/transportation-fact-sheet">The average speed of driving in the US is 32MPH</a>. 60 minutes in an hour / 32 MPH = 1.9 minutes per mile. <a href="https://epi.grants.cancer.gov/atus-met/met.php">It takes about twice your basal metabolic rate to drive</a>. The average person in the US has a basal metabolic rate of about 1605 calories per day. In the time it takes to drive a mile most people burn about (1605BMR *(1.9 minutes/(24 hours * 60 minutes)))*2 = <strong>4.25 calories</strong>.</li>
</ul>
<a href=\"https://www.reuters.com/article/us-autos-emissions/u-s-vehicle-fuel-economy-rises-to-record-24-7-mpg-epa-idUSKBN1F02BX\">The average car from 2016 gets <?php echo $fuel_effiecnty_2016; ?> miles per gallon</a>. <?php echo number_format($gallonofgas); ?>calories / <?php echo $fuel_effiecnty_2016; ?>miles per gallon = 1231 calories per mile. Our numbers from above total 41 + 209 + 4.25 = 254 calories. That pushes the car's calories per mile to 1485 or about 21% greater.
</p>

<hr style="margin-top:25px;margin-bottom:15px;">
<p>
  There are a lot of assumptions made in this calculator that probably make these numbers guesses:</p>
  <ul>
  <li>How <a href="http://css.umich.edu/sites/default/files/css_doc/CSS00-04.pdf">accurate is the 10:1 fossil fuel calories to food calories</a>? How about 5:1 for vegetarians?</li>
	<li><a href="https://www.mepartnership.org/counting-calories-in-agriculture/">If you only take into account farming the ratio is 3:1</a>. Processing, cooking, transport, packaging, etc make up the rest. Should we account for that?</li>
  <li>How much energy is used making cars? How much energy is that per mile of the life of the car?</li>
  <li>Is <a href="https://onlinelibrary.wiley.com/doi/pdf/10.1002/clc.4960130809">Metabolic Equivalent</a> accurate?</li>
  <li>Is the <a href="https://en.wikipedia.org/wiki/Harris%E2%80%93Benedict_equation">basal metabolic rate (BMR) calculation</a> accurate? Should it be gendered? What age and weight ranges is the BMR formula valid for?</li>
  <li>Does walking decrease the need for healthcare? How calorically valuable is health?</li>
  <li>Is a car's consumption of a calorie more or less polluting than a calorie produced for people?</li>
  <li>Packaging contributes some fossil fuel calories to food, but it reduces spoilage. Is it better to have more packaging or less?</li>
</ul>
<hr>

<div class="text-center">  `"Gas Kcal"/((((((10*"Weight") + (6.25*"Height") - (5*"Age") + "Sex")/24)*"METS")/"Speed")*"Fossil Ratio")`  </div><br>

  <table style="margin: 0 auto" class="table-striped table-bordered">
    <tr><th>Formula</th><th>Item</th><th>Value</th></tr>
    <tr><td>Gas Kcal</td><td>Kcal per Gallon of Gas</td><td><?php echo number_format($gallonofgas); ?></td></tr>
    <tr><td>Weight</td><td>Your Weight</td><td><?php echo number_format(kgtolbs($weight)); ?></td></tr>
    <tr><td>Height</td><td>Your Height</td><td><?php echo displayfeetinches(cmtoinches($height)); ?></td></tr>
    <tr><td>Age</td><td>Your Age</td><td><?php echo number_format($age); ?></td></tr>
    <tr><td>Sex</td><td>Your Sex</td><td>female use -161; male use 5</td></tr>
    <tr><td>METS</td><td>Metabolic Equivalent</td><td><?php echo $mets; ?> (<a href="mets.php">see lookup table</a>)</td></tr>
    <tr><td>Speed</td><td>Your Speed</td><td><?php echo round($speed,1); ?></td></tr>
    <tr><td>Fossil Ratio</td><td>Fossil Fuel Ratio</td><td>omnivores use 10; vegetarians use 5</td></tr>

  </table>
  <br>
<script type="text/x-mathjax-config">MathJax.Hub.Config({"HTML-CSS": { scale: 150, linebreaks: { automatic: true } }});</script>
<script src="https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=AM_HTMLorMML"></script>
<?php echo file_get_contents('footer.html'); ?>
