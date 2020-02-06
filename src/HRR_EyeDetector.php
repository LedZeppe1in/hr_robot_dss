<?php


function EyeDetector(& $theFaceData)
{
	
$theFaceData["Characteristics"]["eye"]["right_eye_inner"]["MaxX"]=FaceDataMaxForKey($theFaceData["eye"]["right_eye_inner"],"X")[0];
$theFaceData["Characteristics"]["eye"]["right_eye_inner"]["MaxY"]=FaceDataMaxForKey($theFaceData["eye"]["right_eye_inner"],"Y")[0];
$theFaceData["Characteristics"]["eye"]["right_eye_inner"]["MinX"]=FaceDataMinForKey($theFaceData["eye"]["right_eye_inner"],"X")[0];
$theFaceData["Characteristics"]["eye"]["right_eye_inner"]["MinY"]=FaceDataMinForKey($theFaceData["eye"]["right_eye_inner"],"Y")[0];
$theFaceData["Characteristics"]["eye"]["right_eye_inner"]["NatX"]=422;//здесь и далее опрделено экспертно на основе визуального анализа точек
$theFaceData["Characteristics"]["eye"]["right_eye_inner"]["NatY"]=250;

$theFaceData["Characteristics"]["eye"]["right_eye_outer"]["MaxX"]=FaceDataMaxForKey($theFaceData["eye"]["right_eye_outer"],"X")[0];
$theFaceData["Characteristics"]["eye"]["right_eye_outer"]["MaxY"]=FaceDataMaxForKey($theFaceData["eye"]["right_eye_outer"],"Y")[0];
$theFaceData["Characteristics"]["eye"]["right_eye_outer"]["MinX"]=FaceDataMinForKey($theFaceData["eye"]["right_eye_outer"],"X")[0];
$theFaceData["Characteristics"]["eye"]["right_eye_outer"]["MinY"]=FaceDataMinForKey($theFaceData["eye"]["right_eye_outer"],"Y")[0];
$theFaceData["Characteristics"]["eye"]["right_eye_outer"]["NatX"]=538;
$theFaceData["Characteristics"]["eye"]["right_eye_outer"]["NatY"]=250;

$theFaceData["Characteristics"]["eye"]["right_upper_eyelid"]["MaxX"]=FaceDataMaxForKey($theFaceData["eye"]["right_upper_eyelid"],"X")[0];
$theFaceData["Characteristics"]["eye"]["right_upper_eyelid"]["MaxY"]=FaceDataMaxForKey($theFaceData["eye"]["right_upper_eyelid"],"Y")[0];
$theFaceData["Characteristics"]["eye"]["right_upper_eyelid"]["MinX"]=FaceDataMinForKey($theFaceData["eye"]["right_upper_eyelid"],"X")[0];
$theFaceData["Characteristics"]["eye"]["right_upper_eyelid"]["MinY"]=FaceDataMinForKey($theFaceData["eye"]["right_upper_eyelid"],"Y")[0];
$theFaceData["Characteristics"]["eye"]["right_upper_eyelid"]["NatX"]=505;
$theFaceData["Characteristics"]["eye"]["right_upper_eyelid"]["NatY"]=232;

$theFaceData["Characteristics"]["eye"]["right_lower_eyelid"]["MaxX"]=FaceDataMaxForKey($theFaceData["eye"]["right_lower_eyelid"],"X")[0];
$theFaceData["Characteristics"]["eye"]["right_lower_eyelid"]["MaxY"]=FaceDataMaxForKey($theFaceData["eye"]["right_lower_eyelid"],"Y")[0];
$theFaceData["Characteristics"]["eye"]["right_lower_eyelid"]["MinX"]=FaceDataMinForKey($theFaceData["eye"]["right_lower_eyelid"],"X")[0];
$theFaceData["Characteristics"]["eye"]["right_lower_eyelid"]["MinY"]=FaceDataMinForKey($theFaceData["eye"]["right_lower_eyelid"],"Y")[0];
$theFaceData["Characteristics"]["eye"]["right_lower_eyelid"]["MinYFrame"]=FaceDataMinForKey($theFaceData["eye"]["right_lower_eyelid"],"Y")[1];
$theFaceData["Characteristics"]["eye"]["right_lower_eyelid"]["NatX"]=505;
$theFaceData["Characteristics"]["eye"]["right_lower_eyelid"]["NatY"]=258;


//right_upper_eyelid
Ymoves($theFaceData["eye"]["right_upper_eyelid"],$theFaceData["Characteristics"]["eye"]["right_upper_eyelid"]);

//right_lower_eyelid
Ymoves($theFaceData["eye"]["right_lower_eyelid"],$theFaceData["Characteristics"]["eye"]["right_lower_eyelid"]);


}





// движение N - вверх , S - вниз
function Ymoves(& $theArray,$Characteristics)
{
	$N=count($theArray);
	if ($N<=0) return 0;
	
	$deltaForN=$Characteristics["MinY"]-$Characteristics["NatY"];
	$deltaForS=$Characteristics["MaxY"]-$Characteristics["NatY"];
	
	for ($i=0;$i<$N;$i++)
	{
		if ($theArray[$i]  && $theArray[$i]["Y"] )
		{
			if ($theArray[$i]["Y"]<$Characteristics["NatY"])
			{
				$theArray[$i]["MovementDirection"]="N";
				$theArray[$i]["MovementForce"]=round((($theArray[$i]["Y"]-$Characteristics["NatY"])/$deltaForN),2);
				
			}
			elseif ($theArray[$i]["Y"]>$Characteristics["NatY"])
			{
				$theArray[$i]["MovementDirection"]="S";
				$theArray[$i]["MovementForce"]=round((($theArray[$i]["Y"]-$Characteristics["NatY"])/$deltaForS),2);				
			}
			else
			{
				//ввести погрешность для определения отсутсвтия движения
				$theArray[$i]["MovementDirection"]="X";
				$theArray[$i]["MovementForce"]=0;
			}
		}		
	}
}



function FaceDataMaxForKey($theArray,$theKey)
{
	$N=count($theArray);
	if ($N<=0) return 0;
	
	$max=$theArray[0][$theKey];
	$maxFrame=0;
	for ($i=0;$i<$N;$i++)
	{
		if ($theArray[$i] && $theArray[$i][$theKey])
		{
			if ($theArray[$i][$theKey]>$max) 
			{
				$max=$theArray[$i][$theKey];
				$maxFrame=$i;
			}				
		}		
	}		
	return array(0=>$max,1=>$maxFrame);
}

function FaceDataMinForKey($theArray,$theKey)
{
	$N=count($theArray);
	if ($N<=0) return 0;
	
	$min=$theArray[0][$theKey];
	$minFrame=0;
	
	for ($i=0;$i<$N;$i++)
	{
		if ($theArray[$i] && $theArray[$i][$theKey])
		{
			if ($theArray[$i][$theKey]<$min) 
			{
				$min=$theArray[$i][$theKey];
				$minFrame=$i;
			}				
		}		
	}		
	return array(0=>$min,1=>$minFrame);
}

		
?>