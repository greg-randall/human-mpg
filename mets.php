<?php echo file_get_contents('header.html');

echo "<h3>Metabolic Equivalent Lookup Table.</h3>
<p>Data taken from <a href=\"https://en.wikipedia.org/wiki/Metabolic_equivalent#Epidemiology_and_public_health\">Wikipedia</a>.</p>
<p>Intermediate values interpolated using <a href=\"https://support.office.com/en-us/article/forecast-function-50ca49c9-7b40-4892-94e4-7ad38bbeda99\">Excel's forcast function</a>.</p>";

include('metslookup.php');

echo "<table class=\"table-striped table-bordered\">";
echo "<tr><th>Speed</th><th>METS</th></tr>";
foreach ($metslookup as $speed => $mets_value) {
	echo "<tr><td>$speed</td><td>".number_format($mets_value, 3, '.', '')."</td></tr>";
}
echo"</table><br>";

echo file_get_contents('footer.html'); ?>
