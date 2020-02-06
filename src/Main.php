<?php

 class TInterview
  {
   private $IntArrayOfFrames = array();

   function __Construct(stdClass $JSONData)
    {
     foreach($JSONData as $Name => $Value)
      {
       if ((strpos($Name, 'frame_#') === 0) &&
           (($Value InstanceOf stdClass) === True))
        $this -> IntArrayOfFrames[] = new TFrame($Value);
      }
    }

   public function Frames($Index)
    {
     try
      {
       return $this -> IntArrayOfFrames[$Index];
      }
     catch(Exception $E)
      {
       return Null;
      }
    }

   public function CountOfFrames()
    {
     return count($this -> IntArrayOfFrames);
    }
  }

 class TFrame
  {
   private $IntFace;

   function __Construct(stdClass $RawData)
    {
     $this -> IntFace = $RawData;
    }

   protected static function RetrieveForeheadDescription(stdClass $RawData)
    {
     $Result = Null;
     try
      {
       if ((property_exists($RawData, 'brow') === True) &&
           (property_exists($RawData, 'eyebrow') === True))
        {
         $ForeheadData = $RawData -> brow;
         $EyebrowData = $RawData -> eyebrow;
         if ((property_exists($ForeheadData, 'brow_left') === True) &&
             (property_exists($ForeheadData, 'brow_center') === True) &&
             (property_exists($ForeheadData, 'brow_right') === True) &&
             (is_array($ForeheadData -> brow_left) === True) &&
             (count($ForeheadData -> brow_left) > 1) &&
             (is_array($ForeheadData -> brow_center) === True) &&
             (count($ForeheadData -> brow_center) > 1) &&
             (is_array($ForeheadData -> brow_right) === True) &&
             (count($ForeheadData -> brow_right) > 1) &&
             (property_exists($EyebrowData, 'left_eyebrow_center') === True) &&
             (property_exists($EyebrowData, 'right_eyebrow_center') === True) &&
             (is_array($EyebrowData -> left_eyebrow_center) === True) &&
             (count($EyebrowData -> left_eyebrow_center) > 1) &&
             (is_array($EyebrowData -> right_eyebrow_center) === True) &&
             (count($EyebrowData -> right_eyebrow_center) > 1))
          {
           $Result = new TForehead(new TPoint($ForeheadData -> brow_left[0], $ForeheadData -> brow_left[1], 'Left'),
                                   new TPoint($ForeheadData -> brow_right[0], $ForeheadData -> brow_right[1], 'Right'),
                                   new TPoint($ForeheadData -> brow_center[0], $ForeheadData -> brow_center[1], 'Top'),
                                   new TPoint(ceil(($EyebrowData -> left_eyebrow_center[0] + $EyebrowData -> right_eyebrow_center[0])/2),
                                              ceil(($EyebrowData -> left_eyebrow_center[0] + $EyebrowData -> right_eyebrow_center[1])/2),
                                              'Bottom'));
          }
        }
      }
     catch(Exception $E) {}
     
     return $Result;
    }

   protected static function RetrieveEyeDescription(stdClass $RawData, $KindOfEye)
    {
     try
      {
       return Null;
      }
     catch(Exception $E)
      {
       return Null;
      }
    }

   protected static function RetrieveMouthDescription(stdClass $RawData)
    {
     try
      {
       return Null;
      }
     catch(Exception $E)
      {
       return Null;
      }
    }

   public function Face()
    {
     if (($this -> IntFace InstanceOf TFace) === False)
      {
       $this -> IntFace = new TFace($this -> IntFace,
                                    self::RetrieveForeheadDescription($this -> IntFace),
                                    self::RetrieveEyeDescription($this -> IntFace, 'Left'),
                                    self::RetrieveEyeDescription($this -> IntFace, 'Right'),
                                    self::RetrieveMouthDescription($this -> IntFace));
      }
     
     return $this -> IntFace;
    }

   public function CreateFaceDescriptionAsSetFacts() {return Null;}
   public function Normalize() {return Null;}
  }

 abstract class TRawDataContainer
  {
   private $IntRawData = array();

   function __Construct(stdClass $JSONData)
    {
     $this -> IntRawData = $JSONData;
    }

   public function RawData() {return $this -> IntRawData;}
  }

 class TFace extends TRawDataContainer
  {
   private $IntForehead;
   private $IntLeftEye;
   private $IntRightEye;
   private $IntMouth;

   function __Construct(stdClass $RawData,
                        TForehead $NewForehead = Null,
                        TEye $NewLeftEye = Null,
                        TEye $NewRightEye = Null,
                        TMouth $NewMouth = Null)
    {
     parent::__Construct($RawData);

     $this -> IntForehead = $NewForehead;
     $this -> IntLeftEye = $NewLeftEye;
     $this -> IntRightEye = $NewRightEye;
     $this -> IntMouth = $NewMouth;
    }

   public function RawData() {return $this -> IntRawData;}

   public function Forehead() {return $this -> IntForehead;}
   public function LeftEye() {return $this -> IntLeftEye;}
   public function RightEye() {return $this -> IntRightEye;}
   public function Mouth() {return $this -> IntMouth;}
  }

 class TPoint
  {
   private $IntX;
   private $IntY;
   private $IntIndex;

   function __Construct($NewX, $NewY, $NewIndex)
    {
     $this -> IntX = $NewX;
     $this -> IntY = $NewY;
     $this -> IntIndex = $NewIndex;
    }
    
   public function X() {return $this -> IntX;}
   public function Y() {return $this -> IntY;}
   public function Index() {return $this -> IntIndex;}
  }

 interface ILine
  {
   public function Points($Index);
   public function CountOfPoints();
  }

 interface IRegion
  {
   public function Height();
   public function Width();
  }

 class TArcTypeRegion implements IRegion
  {
   private $IntLeft;
   private $IntRight;
   private $IntTop;

   function __Construct(TPoint $NewLeft,
                        TPoint $NewRight,
                        TPoint $NewTop)
    {
     $this -> IntLeft = $NewLeft;
     $this -> IntRight = $NewRight;
     $this -> IntTop = $NewTop;
    }

   public function Left() {return $this -> IntLeft;}
   public function Right() {return $this -> IntRight;}
   public function Top() {return $this -> IntTop;}

   // implementation of IRegion - begin

   public function Height() {return abs($this -> Top() -> Y() - ceil(($this -> Left() -> Y() + $this -> Right() -> Y())/2));}
   public function Width() {return abs($this -> Left() -> X() - $this -> Right() -> X());}

   // implementation of IRegion - end
  }

 class TPoligonTypeRegion extends TArcTypeRegion
  {
   private $IntBottom;

   function __Construct(TPoint $NewLeft,
                        TPoint $NewRight,
                        TPoint $NewTop,
                        TPoint $NewBottom)
    {
     parent::__Construct($NewLeft, $NewRight, $NewTop);

     $this -> IntBottom = $NewBottom;
    }

   public function Bottom() {return $this -> IntBottom;}

   // implementation of IRegion - begin

   public function Height() {return abs($this -> Top() -> Y() - $this -> Bottom() -> Y());}

   // implementation of IRegion - end
  }

 class TForehead extends TPoligonTypeRegion
  {
   public static function RetrieveRate($Value)
    {
     return 10;
    }
  }

 class TEye extends TPoligonTypeRegion
  {
   private $IntBrow;
   private $IntPupil;

   function __Construct(TPoint $NewLeft,
                        TPoint $NewRight,
                        TPoint $NewTop,
                        TPoint $NewBottom,
                        TArcTypeRegion $NewBrow,
                        TPoint $NewPupil)
    {
     parent::__Construct($NewLeft, $NewRight, $NewTop, $NewBottom);

     $this -> IntBrow = $NewBrow;
     $this -> IntPupil = $NewPupil;
    }

   public function Brow() {return $this -> IntBrow;}
   public function Pupil() {return $this -> IntPupil;}
  }

 class TLip extends TArcTypeRegion
  {
  }

 class TMouth extends TPoligonTypeRegion
  {
   private $IntUpperLip;
   private $IntLowerLip;

   public function UpperLip() {return $this -> IntUpperLip;}
   public function LowerLip() {return $this -> IntLowerLip;}
  }

 abstract class TVariation
  {
   private $IntSource;
   private $IntPositive;
   private $IntValue;
   private $IntEvaluation;
   private $IntFrameIndex;

   protected $IntTrend = array();

   function __Construct($NewSource, $NewPositive, $NewValue, $NewRate, $NewFrameIndex)
    {
     $this -> IntSource = $NewSource;
     $this -> IntPositive = $NewPositive;
     $this -> IntValue = $NewValue;
     $this -> IntRate = $NewRate;
     $this -> IntFrameIndex = $NewFrameIndex;
    }

   public function Source() {return $this -> IntSource;}
   public function Positive() {return $this -> IntPositive;}
   public function Value() {return $this -> IntValue;}
   public function Rate() {return $this -> IntRate;}
   public function FrameIndex() {return $this -> IntFrameIndex;}

   public function RangeOfFrames()
    {
     $LengthOfTrend = $this -> LengthOfTrend();
     if ($LengthOfTrend === 0)
      $IndexOfLastFrame = $this -> IntFrameIndex;
     else
      $IndexOfLastFrame = $this -> Trend($LengthOfTrend - 1) -> FrameIndex();

     return array($this -> FrameIndex(), $IndexOfLastFrame);
    }

   public function Trend($Index)
    {
     try
      {
       return $this -> IntTrend[$Index];
      }
     catch(Exception $E)
      {
       return Null;
      }
    }
    
   public function LengthOfTrend()
    {
     return count($this -> IntTrend);
    }
    
   public function AddVariationToTrend(TVariation $SingleVariation)
    {
     $this -> IntTrend[] = $SingleVariation;
    }

   public function TotalValue()
    {
     $Result = $this -> Value();

     $LengthOfTrend = $this -> LengthOfTrend();
     for ($i = 0; $i < $LengthOfTrend; $i++)
      {
       $Result = $Result + $this -> Trend($i) -> Value();
      }

     return $Result;
    }

   public function TotalRate()
    {
     $NameOfClass = $this -> Source();
     if (class_exists($this -> Source()) === True)
      return $NameOfClass::RetrieveRate($this -> TotalValue());
     else
      return Null;
    }

   public function PositiveAsText()
    {
     if ($this -> IntPositive === True)
      return '+';
     elseif ($this -> IntPositive === False)
      return '-';
     else
      return '=';
    }
  }

 class THeightVariation extends TVariation
  {
   public static function Calculation($Source, IRegion $State1, IRegion $State2, THeightVariation $Current = Null, $IndexOfFrame)
    {
     if ($State2 -> Height() > $State1 -> Height())
      $Positive = True;
     elseif ($State2 -> Height() === $State1 -> Height())
      $Positive = Null;
     else
      $Positive = False;

     $Value = abs($State2 -> Height() - $State1 -> Height());

     if (class_exists($Source) === True)
      $Rate = $Source::RetrieveRate($Value);
     else
      $Rate = Null;
      
     $Result = new THeightVariation($Source, $Positive, $Value, $Rate, $IndexOfFrame);

     if (isset($Current) === True)
      {
       if ($Result -> Positive() === $Current -> Positive())
        {
         $Current -> AddVariationToTrend($Result);
         $Result = Null;
        }
      }

     return $Result;
    }
  }

 class TWidthVariation extends TVariation
  {
   public static function Calculation($Source, IRegion $State1, IRegion $State2, TWidthVariation $Current = Null, $IndexOfFrame)
    {
     if ($State2 -> Width() > $State1 -> Width())
      $Positive = True;
     elseif ($State2 -> Width() === $State1 -> Width())
      $Positive = Null;
     else
      $Positive = False;

     $Value = abs($State2 -> Width() - $State1 -> Width());

     if (class_exists($Source) === True)
      $Rate = $Source::RetrieveRate($Value);
     else
      $Rate = Null;

     $Result = new TWidthVariation($Source, $Positive, $Value, $Rate, $IndexOfFrame);

     if (isset($Current) === True)
      {
       if ($Result -> Positive() === $Current -> Positive())
        {
         $Current -> AddVariationToTrend($Result);
         $Result = Null;
        }
      }

     return $Result;
    }
  }

 require(realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.
                  'RawData.php'));

 $RawDataAsJSONObject = json_decode($RawData);
 
 $Interview = new TInterview($RawDataAsJSONObject);
 $CountOfFrames = $Interview -> CountOfFrames();
 echo 'CountOfFrames: '.$CountOfFrames.PHP_EOL;
 
 $Variations = array();
 $IndicesOfDamagedFrames = array();
 $CurrentHeightVariation = Null;
 $CurrentWidthVariation = Null;
 $CurrentVariation = Null;
 $ForeheadPreviousState = Null;
 for ($i = 0; $i < $CountOfFrames; $i++)
  {
   $Frame = $Interview -> Frames($i);
   $Face = $Frame -> Face();
   $Forehead = $Face -> Forehead();

   if (isset($Forehead) === False)
    {
     $IndicesOfDamagedFrames[] = $i;
     $ForeheadPreviousState = Null;
     continue;
    }
    
   if (isset($ForeheadPreviousState) === True)
    {
     $HeightVariation = THeightVariation::Calculation('TForehead',
                                                      $ForeheadPreviousState,
                                                      $Forehead,
                                                      $CurrentHeightVariation,
                                                      $i);
                                                      
     $WidthVariation = TWidthVariation::Calculation('TForehead',
                                                    $ForeheadPreviousState,
                                                    $Forehead,
                                                    $CurrentWidthVariation,
                                                    $i);
     if (isset($HeightVariation) === True)
      {
       $CurrentHeightVariation = $HeightVariation;
       $Variations[] = $HeightVariation;
      }
      
     if (isset($WidthVariation) === True)
      {
       $CurrentWidthVariation = $WidthVariation;
       $Variations[] = $WidthVariation;
      }
    }
    
   $ForeheadPreviousState = $Forehead;
  }

 $CountOfVariations = count($Variations);
 for ($i = 0; $i < $CountOfVariations; $i++)
  {
   if (($Variations[$i] -> LengthOfTrend() > 0) ||
       ($Variations[$i] -> TotalValue() > 5))
    {
     $RangeOfFrames = $Variations[$i] -> RangeOfFrames();
     echo get_class($Variations[$i]).': '.$Variations[$i] -> PositiveAsText().' '.
          'Delta: '.$Variations[$i] -> TotalValue().' '.
          'LengthOfTrend: '.($Variations[$i] -> LengthOfTrend() + 1).' '.
          'Range: '.$RangeOfFrames[0].' - '.$RangeOfFrames[1].PHP_EOL;
    }
  }
  
 echo PHP_EOL;
 echo 'IndicesOfDamagedFrames: '.print_r($IndicesOfDamagedFrames, True).PHP_EOL;

?>
