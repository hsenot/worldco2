<?php
	$output = isset($_REQUEST['output']) ? $_REQUEST['output'] : 'json';

	//require_once 'reader.php';
	date_default_timezone_set('Europe/Dublin');

	$dat = date("d/m/Y");
	$url = "http://www.eirgrid.com/operations/systemperformancedata/download.jsp?download=Y&startdate=".$dat."&enddate=".$dat."&proc=data_pack.getco2intensityforadayiphone&templatename=CO2%20Intensity&columnnames=Time,g%20CO&#8322;/KWh&prevurl=http://www.eirgrid.com/operations/systemperformancedata/co2intensity/";
	//echo $url;
	// Download of the CSV file
	$tmpDir = "/tmp";
	$csvFile = $tmpDir."/ie-current.csv";
	$csvStr = file_get_contents($url);
	file_put_contents($csvFile, $csvStr);

	$row = 1;
	if (($handle = fopen($csvFile, "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$num = count($data);
        		$row++;
        		for ($c=0; $c < $num; $c++) {
				//echo "col".$c.":".$data[$c]."\n";
				if ($c == 1)
				{
					if (trim($data[$c]) !== "null")
					{
						// Needs to translate to a tCO2/MWh
						$val_arr = (string) (round(trim($data[$c])/1000,3));
						// Update time is in the second part of the time piece
						$timepiece = explode(' ',trim($data[0]));
						if (isset($timepiece[1]))
						{
							$ut = (string) ($timepiece[1]);
						}
	
					}
				}
        		}
    		}
		fclose($handle);
	}

	// Last value is the last known emission factor
	$arr['co2_intensity']=$val_arr;
	$arr['timestamp']=$ut;

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
