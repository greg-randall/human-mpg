<?php
function get_client_ip() { // http://thephpcode.blogspot.com/2009/01/php-getting-secondary-internet-protocol.html
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function lbstokg($input){
	$output = $input * 0.453592;
	return($output);
}
function kgtolbs($input){
	$output = $input * 2.20462;
	return($output);
}
function cmtoinches($input){
	$output = $input * 0.393701;
	return($output);
}
function inchestocm($input){
	$output = $input * 2.54;
	return($output);
}

function displayfeetinches($input){
	$feet = (int)($input / 12);
	$inches = (int) ($input - ($feet * 12));
	if($inches != 0){
		echo $feet ."' ". $inches .'"';
	}else {
		echo $feet ."'";
	}
}

?>
