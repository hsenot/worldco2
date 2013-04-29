<?php

	$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 'current';
	$output = isset($_REQUEST['output']) ? $_REQUEST['output'] : 'json';

	//require_once 'reader.php';
	// Timezone for the San Diego observatory is the same as Los Angeles's
	date_default_timezone_set('America/Los_Angeles');

	$dat = date("d/m/Y");
	$dat2 = date("Y-m-d");
	$url = "http://keelingcurve.ucsd.edu/";
	//echo $url;
	// Download of the zip file
	$tmpDir = "/tmp";
	$htmlFile = $tmpDir."/current.html";
	$htmlStr = file_get_contents($url);
//	echo $htmlStr;

	// Scraping the value (3 digits assumed) in front of the first occurrence of gCO2/kWh
	$str_co2=substr($htmlStr,strpos($htmlStr,"Current reading:")+17,6);
	$arr['co2_ppm']=(string) ($str_co2);

	// Persistence  of the result in a JSON file
	file_put_contents(dirname(__FILE__)."/co2.json",json_encode($arr));

	if ($output == "json")
	{
		header("Content-Type: application/json");
		echo json_encode($arr);
	}
	else
	{
		print "Finished";
	}
?>
