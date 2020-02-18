<?php
//****************** HR-Robot Project ***********************
//2020/02/17
//Yurin A.Y.
//************** exported from PKBD ****************
// version: 4.2018.0201.6 
// knowledge base:
// info:

//****************** classes ***********************
class brow_1{
 var $brow_width;
 function Init(){
  $this->brow_width = "";
 }
}
class eyebrow_1{
 var $eyebrow_movement;
 function Init(){
  $this->eyebrow_movement = "";
 }
}
class eye_1{
 var $left_eye_width;
 var $right_eye_width;
 var $left_eye_upper_eyelid_movement;
 var $right_eye_upper_eyelid_movement;
 function Init(){
  $this->left_eye_width = "";
  $this->right_eye_width = "";
  $this->left_eye_upper_eyelid_movement = "";
  $this->right_eye_upper_eyelid_movement = "";
 }
}
class mouth_1{
 var $mouth_length;
 var $mouth_form;
 var $mouth_corners_movement;
 function Init(){
  $this->mouth_length = "";
  $this->mouth_form = "";
  $this->mouth_corners_movement = "";
 }
}
class emotion_1{
 var $name;
 var $fired_rule;
 function Init(){
  $this->name = "none";
  $this->fired_rule = "none";
 }
}
//******** Initialization (facts) ******************
$brow_1_ = new brow_1;
$brow_1_->Init();
$eyebrow_1_ = new eyebrow_1;
$eyebrow_1_->Init();
$eye_1_ = new eye_1;
$eye_1_->Init();
$mouth_1_ = new mouth_1;
$mouth_1_->Init();
$emotion_1_ = new emotion_1;
$emotion_1_->Init();

//**************** rules ***************************
function RunKB($json_file){
//-------------------------------------------------------
//--------------- load data from json -------------------
$json = file_get_contents($json_file, true);
$FaceData_=json_decode($json, true);

$brow_1_->brow_width = $FaceData_['frames']['0']["brow"]["brow_width"]["val"]; 
//echo '$brow_1_->brow_width '.$brow_1_->brow_width.'<br>';
//print_r($FaceData_['frames']);
$eyebrow_1_->eyebrow_movement = $FaceData_['frames']['0']["eyebrow"]["eyebrow_movement"]["val"]; 
//echo '$eyebrow_1_->eyebrow_movement '.$eyebrow_1_->eyebrow_movement.'<br>';
$eye_1_->left_eye_width = $FaceData_['frames']['0']["eye"]["left_eye_width"]["val"]; 
//echo '$eye_1_->left_eye_width '.$eye_1_->left_eye_width.'<br>';
$eye_1_->right_eye_width = $FaceData_['frames']['0']["eye"]["right_eye_width"]["val"]; 
//echo '$eye_1_->right_eye_width '.$eye_1_->right_eye_width.'<br>';
$eye_1_->left_eye_upper_eyelid_movement = $FaceData_['frames']['0']["eye"]["left_eye_upper_eyelid_movement"]["val"]; 
//echo '$eye_1_->left_eye_upper_eyelid_movement '.$eye_1_->left_eye_upper_eyelid_movement.'<br>';
$eye_1_->right_eye_upper_eyelid_movement = $FaceData_['frames']['0']["eye"]["right_eye_upper_eyelid_movement"]["val"]; 
//echo '$eye_1_->right_eye_upper_eyelid_movement '.$eye_1_->right_eye_upper_eyelid_movement.'<br>';
$mouth_1_->mouth_length = $FaceData_['frames']['0']["mouth"]["mouth_length"]["val"]; 
//echo '$mouth_1_->mouth_length '.$mouth_1_->mouth_length.'<br>';
$mouth_1_->mouth_form = $FaceData_['frames']['0']["mouth"]["mouth_form"]["val"]; 
//echo '$mouth_1_->mouth_form '.$mouth_1_->mouth_form.'<br>';
$mouth_1_->mouth_corners_movement = $FaceData_['frames']['0']["mouth"]["mouth_corners_movement"]["val"]; 
//echo '$mouth_1_->mouth_corners_movement '.$mouth_1_->mouth_corners_movement.'<br>';
//-------------------------------------------------------

//fear-p-01
if (
(($brow_1_->brow_width == "decrease"))
 and
(($eyebrow_1_->eyebrow_movement == "up"))
 and
(($eye_1_->left_eye_width == "increase") and ($eye_1_->left_eye_upper_eyelid_movement == "up"))
 and
(($mouth_1_->mouth_length == "increase") and ($mouth_1_->mouth_form == "ellipse"))
){
 $emotion_1_->name = "fear";
 $emotion_1_->fired_rule = "fear-p-01";
}
//fear-p-02
if (
(($brow_1_->brow_width == "decrease"))
 and
(($eyebrow_1_->eyebrow_movement == "to_center"))
 and
(($eye_1_->right_eye_width == "increase") and ($eye_1_->right_eye_upper_eyelid_movement == "up"))
 and
(($mouth_1_->mouth_length == "increase") and ($mouth_1_->mouth_form == "ellipse"))
){
 $emotion_1_->name = "fear";
 $emotion_1_->fired_rule = "fear-p-02";
}
//fear-p-03
if (
(($brow_1_->brow_width == "decrease"))
 and
(($eyebrow_1_->eyebrow_movement == "up"))
 and
(($eye_1_->left_eye_width == "increase") and ($eye_1_->right_eye_width == "increase"))
 and
(($mouth_1_->mouth_length == "increase") and ($mouth_1_->mouth_form == "ellipse") and ($mouth_1_->mouth_corners_movement == "aside"))
){
 $emotion_1_->name = "fear";
 $emotion_1_->fired_rule = "fear-p-03";
}
//fear-p-04
if (
(($brow_1_->brow_width == "decrease"))
 and
(($eyebrow_1_->eyebrow_movement == "up"))
 and
(($eye_1_->left_eye_width == "increase") and ($eye_1_->left_eye_upper_eyelid_movement == "up"))
 and
(($mouth_1_->mouth_length == "increase") and ($mouth_1_->mouth_form == "ellipse") and ($mouth_1_->mouth_corners_movement == "aside"))
){
 $emotion_1_->name = "fear";
 $emotion_1_->fired_rule = "fear-p-04";
}
//fear-p-05
if (
(($brow_1_->brow_width == "decrease"))
 and
(($eyebrow_1_->eyebrow_movement == "to_center"))
 and
(($eye_1_->right_eye_width == "increase") and ($eye_1_->right_eye_upper_eyelid_movement == "up"))
 and
(($mouth_1_->mouth_form == "ellipse") and ($mouth_1_->mouth_corners_movement == "aside"))
){
 $emotion_1_->name = "fear";
 $emotion_1_->fired_rule = "fear-p-05";
}
//fear-p-06
if (
(($brow_1_->brow_width == "decrease"))
 and
(($eyebrow_1_->eyebrow_movement == "up"))
 and
(($eye_1_->left_eye_width == "increase") and ($eye_1_->right_eye_width == "increase") and ($eye_1_->left_eye_upper_eyelid_movement == "up") and ($eye_1_->right_eye_upper_eyelid_movement == "up"))
 and
(($mouth_1_->mouth_form == "ellipse") and ($mouth_1_->mouth_corners_movement == "aside"))
){
 $emotion_1_->name = "fear";
 $emotion_1_->fired_rule = "fear-p-06";
}
//fear-p-07
if (
(($brow_1_->brow_width == "decrease"))
 and
(($eyebrow_1_->eyebrow_movement == "up"))
 and
(($eye_1_->left_eye_width == "increase") and ($eye_1_->right_eye_width == "increase"))
 and
(($mouth_1_->mouth_length == "decrease") and ($mouth_1_->mouth_form == "ellipse") and ($mouth_1_->mouth_corners_movement == "aside"))
){
 $emotion_1_->name = "fear";
 $emotion_1_->fired_rule = "fear-p-07";
}
//fear-p-08
if (
(($brow_1_->brow_width == "decrease"))
 and
(($eyebrow_1_->eyebrow_movement == "up"))
 and
(($eye_1_->left_eye_width == "increase"))
 and
(($mouth_1_->mouth_length == "none") and ($mouth_1_->mouth_form == "ellipse") and ($mouth_1_->mouth_corners_movement == "aside"))
){
 $emotion_1_->name = "fear";
 $emotion_1_->fired_rule = "fear-p-08";
}
//fear-p-09
if (
(($brow_1_->brow_width == "decrease"))
 and
(($eyebrow_1_->eyebrow_movement == "to_center"))
 and
(($eye_1_->left_eye_width == "increase") and ($eye_1_->right_eye_width == "increase"))
 and
(($mouth_1_->mouth_length == "increase") and ($mouth_1_->mouth_form == "ellipse") and ($mouth_1_->mouth_corners_movement == "aside"))
){
 $emotion_1_->name = "fear";
 $emotion_1_->fired_rule = "fear-p-09";
}
//fear-p-10
if (
(($brow_1_->brow_width == "decrease"))
 and
(($eyebrow_1_->eyebrow_movement == "to_center"))
 and
(($eye_1_->left_eye_width == "increase") and ($eye_1_->right_eye_width == "increase") and ($eye_1_->left_eye_upper_eyelid_movement == "up") and ($eye_1_->right_eye_upper_eyelid_movement == "up"))
 and
(($mouth_1_->mouth_form == "ellipse") and ($mouth_1_->mouth_corners_movement == "aside"))
){
 $emotion_1_->name = "fear";
 $emotion_1_->fired_rule = "fear-p-10";
}
//fear-p-11
if (
(($brow_1_->brow_width == "decrease"))
 and
(($eyebrow_1_->eyebrow_movement == "to_center"))
 and
(($eye_1_->left_eye_width == "increase") and ($eye_1_->right_eye_width == "increase"))
 and
(($mouth_1_->mouth_length == "decrease") and ($mouth_1_->mouth_form == "ellipse") and ($mouth_1_->mouth_corners_movement == "aside"))
){
 $emotion_1_->name = "fear";
 $emotion_1_->fired_rule = "fear-p-11";
}
 
if($emotion_1_->name == ""){
  $emotion_1_->name = "none";
  $emotion_1_->fired_rule = "none";
 }
 
 return $emotion_1_;
}
 //--------------------------------------------------------------
 $FaceData = RunKB('test_fear-n-17.json');
 echo $FaceData->name.' '.$FaceData->fired_rule;
 //--------------------------------------------------------------

?>
