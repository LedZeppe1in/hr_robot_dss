<?php
	include_once 'HRR_EyeDetector.php';		
	
 
header('Content-Type: application/json');
 
 
if (isset($_REQUEST['FaceSourceDataFileName']) === False)
{	
	$ReturnData= array("success"=>false,
						"serverfilename" => "file name was not sent"
                    );
	
	echo json_encode($ReturnData);  
 
	return;  
}


$TheNameOfDataFile= $_REQUEST['FaceSourceDataFileName'];
//$thePath=dirname(__FILE__, 1);
$thePathToFile='JsonDataFiles/'.$TheNameOfDataFile;

if (!file_exists($thePathToFile))
{	
	$ReturnData= array("success"=>False,
					"serverfilename" => $TheNameOfDataFile,
					"fileexist"=>False);

	echo json_encode($ReturnData);		
}

$FaceData=json_decode(file_get_contents($thePathToFile),true);

EyeDetector($FaceData);

$thePathToNewFile='JsonDataFiles/FaceDataArrayDetections.json';
$Writed=True;

if (file_put_contents($thePathToNewFile, json_encode($FaceData))==FALSE) $Writed=False; 



		$ReturnData= array("success"=>$Writed,					
					"TestData"=>$FaceData["eye"]["right_lower_eyelid"][159]
                    );
	
	
	
	
	echo json_encode($ReturnData);		

		
		
		
?>