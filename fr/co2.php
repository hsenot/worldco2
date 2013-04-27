<?php

	$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 'current';
	$output = isset($_REQUEST['output']) ? $_REQUEST['output'] : 'json';

	//require_once 'reader.php';
	date_default_timezone_set('UTC');

	$dat = date("d/m/Y");
	$dat2 = date("Y-m-d");
	$url = "http://www.rte-france.com/curves/eco2mixWeb?type=co2&date=".$dat."&nocache=".rand(1,10000)/10000;
	//echo $url;
	// Download of the zip file
	$tmpDir = "/tmp";
	$xmlFile = $tmpDir."/current.xml";
	$xmlStr = file_get_contents($url);
	//file_put_contents($xmlFile, $xmlStr);

	$xml = new SimpleXMLElement($xmlStr);
	//print_r($xml);
	$current_mix =$xml->xpath("//mixtr[@date='".$dat2."']/type");
	//print_r($current_mix);

	$arr=array();
	$total=0;
	$total_prev=0;
	// Tree traversing for better presentation of the JSON
	foreach ($current_mix as $node){
		if ($mode == "full"){
			//print_r($node);
			$val_arr = array();
			foreach ($node->valeur as $nv){
				//print_r($nv);
				$val_arr[(string) $nv->attributes()->periode]=(string) $nv;
			}
		}
		else
		{
			$i=-1;
			foreach ($node->valeur as $nv){
                                //print_r($nv);
                                $val_arr=(string) ($nv/1000);
				$i++;
                        }
			$updatePeriod=$i;
		}
		//print_r($val_arr);
		$arr['co2_factor']=$val_arr;
	}
	
	// Adding the total
	if ($mode <> "full")
	{
		$ut = ((int) ($updatePeriod / 4)) .":". (($updatePeriod % 4)*15);
		if ($updatePeriod % 4 == 0)
		{
			$ut = $ut . "0";
		}
		$arr['timestamp']=(string) $ut;
	}

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
