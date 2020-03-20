<?php

/**
 * FacialFeatureDetector - класс обнаружения лицевых признаков.
 */
class FacialFeatureDetector
{
    /** определение интенсивности проявления признака
     * @param $val1 - диапазон значений
     * @param $val2 - текущее значение
     * @return float|int - интенсивность проявления по относительной шкале от 1 до 5
     */
    public function getForce($val1, $val2)
    {
        $af = $val1 / 5;
        $res = abs(round($val2 / $af));

        return $res;
    }

    /**
     * Вычисляет новую характеристику с именем $newFacialCharacteristicsName в массиве $facialCharacteristics
     * с учетом разбиений на фреймы.
     *
     * @param $facialCharacteristics - массив харрактеристик лица
     * @param $newFacialCharacteristicsName - название новой харрактеристики лица
     * @param $facialCharacteristics1 - первый массив с характеристиками лицевой точки
     * @param $key1 - координата точки из первого массива
     * @param $facialCharacteristics2 - второй массив с характеристиками лицевой точки
     * @param $key2 - координата точки из второго массива
     * @return bool - возвращаемое значение
     */
    public function addOneDimDistance($facialCharacteristics, $newFacialCharacteristicsName,
                                      $facialCharacteristics1, $key1, $facialCharacteristics2, $key2)
    {
        $facialCharacteristics1Number = count($facialCharacteristics1);
        if ($facialCharacteristics1Number <= 0)
            return false;

        $facialCharacteristics2Number = count($facialCharacteristics2);
        if ($facialCharacteristics2Number <= 0)
            return false;

        if ($facialCharacteristics1Number != $facialCharacteristics2Number)
            return false;

        for ($i = 0; $i < $facialCharacteristics1Number; $i++) {
 //           $facialCharacteristics[] = array();
            if ($facialCharacteristics1[$i] && $facialCharacteristics1[$i][$key1] &&
                $facialCharacteristics2[$i] && $facialCharacteristics2[$i][$key2])
                $facialCharacteristics[$i][$newFacialCharacteristicsName] = $facialCharacteristics1[$i][$key1] -
                    $facialCharacteristics2[$i][$key2];
        }

        return $facialCharacteristics;
    }

    /**
     * Обработка изменений значений характеристики лица. Увеличение: +, уменьшение: -.
     * После выполнения добавляет в массив значения с ключом WidthChange и WidthChangeForce для второй размерности
     * для каждого элемента $facialCharacteristics.
     *
     * @param $facialCharacteristics - массив с характеристикой лица
     * @param $key - название характеристики
     * @param $max - максимальное значение характеристики
     * @param $min - минимальное значение характеристики
     * @param $nat - нормальное значение характеристики
     * @return bool - возвращаемое значение
     */
    public function moveD($facialCharacteristics, $key, $max, $min, $nat)
    {
//        $targetFaceData = $facialCharacteristics;
        $facialCharacteristicsNumber = count($facialCharacteristics);

        if ($facialCharacteristicsNumber <= 0)
            return false;

        $deltaForMinus = $min - $nat;
        $deltaForPlus = $max - $nat;
//        $scale = $max - $min;

        for ($i = 0; $i < $facialCharacteristicsNumber; $i++) {
            if ($facialCharacteristics[$i] && $facialCharacteristics[$i][$key]) {
                if ($facialCharacteristics[$i][$key] < $nat) {
                    // Уменьшение ширины
                    $targetFaceData[$i]["Force"] = $this->getForce(
                        $deltaForMinus, abs($facialCharacteristics[$i][$key]- $nat) );
                    $targetFaceData[$i]["WidthChange"] = "-";

                    $facialCharacteristics[$i]["WidthChange"] = "-";
                    $facialCharacteristics[$i]["WidthChangeForce"] = round(
                        (($facialCharacteristics[$i][$key] - $nat) / $deltaForMinus),
                        2
                    );

                } elseif ($facialCharacteristics[$i][$key] > $nat) {
                    // Увеличение ширины
                    $facialCharacteristics[$i]["WidthChange"] = "+";
                    $targetFaceData[$i]["Force"] = $this->getForce(
                        $deltaForPlus, abs($facialCharacteristics[$i][$key]- $nat));
                    $targetFaceData[$i]["WidthChange"] = "+";


                    $facialCharacteristics[$i]["WidthChangeForce"] = round(
                        (($facialCharacteristics[$i][$key] - $nat) / $deltaForPlus),
                        2
                    );
                } else {
                    // Ввести погрешность для определения отсутсвтия движения
                    $facialCharacteristics[$i]["WidthChange"] = "X";
                    $facialCharacteristics[$i]["WidthChangeForce"] = 0;
                    $targetFaceData[$i]["Force"] = 0;
                    $targetFaceData[$i]["WidthChange"] = "none";
                }
            }
        }
  //print_r($targetFaceData);
        return $targetFaceData;
    }

    /**
     * Обработка вертикальных движений для указанной точки. Движение: N - вверх, S - вниз.
     * После выполнения добавляет в массив значения с ключом MovementDirection и MovementForce для
     * второй размерности в каждой точки.
     *
     * @param $facialLandmarkCharacteristics - массив с характеристиками лицевой точки
     * @param $characteristics - максимальное, минимальное и нормальное положение по Y
     * @return bool - возвращаемое значение
     */
    public function moveY($facialLandmarkCharacteristics, $characteristics)
    {
        $facialLandmarkCharacteristicsNumber = count($facialLandmarkCharacteristics);
        if ($facialLandmarkCharacteristicsNumber <= 0)
            return false;

        $deltaForN = $characteristics["MinY"] - $characteristics["NatY"];
        $deltaForS = $characteristics["MaxY"] - $characteristics["NatY"];
 //       $scale = $characteristics["MaxY"] - $characteristics["MinY"];

        for ($i = 0; $i < $facialLandmarkCharacteristicsNumber; $i++) {
            if ($facialLandmarkCharacteristics[$i] && $facialLandmarkCharacteristics[$i]["Y"]) {
                if ($facialLandmarkCharacteristics[$i]["Y"] < $characteristics["NatY"]) {
                    $targetFaceData[$i]["Force"] = $this->getForce(
                        $deltaForN, abs($facialLandmarkCharacteristics[$i]["Y"]
                        - $characteristics["NatY"]) );
                    $targetFaceData[$i]["MovementDirection"] = "N";

                    $facialLandmarkCharacteristics[$i]["MovementDirection"] = "N";
                    $facialLandmarkCharacteristics[$i]["MovementForce"] = round(
                        (($facialLandmarkCharacteristics[$i]["Y"] - $characteristics["NatY"]) / $deltaForN),
                        2
                    );
                } elseif ($facialLandmarkCharacteristics[$i]["Y"] > $characteristics["NatY"]) {
                    $targetFaceData[$i]["Force"] = $this->getForce(
                        $deltaForS, abs($facialLandmarkCharacteristics[$i]["Y"]
                        - $characteristics["NatY"]) );
                    $targetFaceData[$i]["MovementDirection"] = "S";


                    $facialLandmarkCharacteristics[$i]["MovementDirection"] = "S";
                    $facialLandmarkCharacteristics[$i]["MovementForce"] = round(
                        (($facialLandmarkCharacteristics[$i]["Y"] - $characteristics["NatY"]) / $deltaForS),
                        2
                    );
                } else {
                    // Ввести погрешность для определения отсутсвтия движения
                    $facialLandmarkCharacteristics[$i]["MovementDirection"] = "X";
                    $facialLandmarkCharacteristics[$i]["MovementForce"] = 0;
                    $targetFaceData[$i]["Force"] = 0;
                    $targetFaceData[$i]["MovementDirection"] = "none";

                }
            }
        }

        return $targetFaceData;
    }

    /**
     * Вычисление среднего значения характеристики лица за время наблюдений.
     *
     * @param $facialCharacteristics - массив с характеристикой лица
     * @param $key - название характеристики
     * @return float|bool - возвращаемое значение
     */
    public function getFaceDataAvrForKey($facialCharacteristics, $key)
    {
        $facialCharacteristicsNumber = count($facialCharacteristics);
        if ($facialCharacteristicsNumber <= 0)
            return false;

        $avr = 0;
        for ($i = 0; $i < $facialCharacteristicsNumber; $i++)
            if ($facialCharacteristics[$i] && $facialCharacteristics[$i][$key])
                $avr += $facialCharacteristics[$i][$key];

        return round($avr / $facialCharacteristicsNumber, 0);
    }

    /**
     * Вычисление максимального значения характеристики лица за время наблюдений.
     *
     * @param $facialCharacteristics - массив с характеристикой лица
     * @param $key - название характеристики
     * @return array|bool - возвращаемое значение
     */
    public function getFaceDataMaxForKey($facialCharacteristics, $key)
    {
        $facialCharacteristicsNumber = count($facialCharacteristics);
        if ($facialCharacteristicsNumber <= 0)
            return false;

        $max = $facialCharacteristics[0][$key];
        $maxFrame = 0;
        for ($i = 0; $i < $facialCharacteristicsNumber; $i++)
            if ($facialCharacteristics[$i] && $facialCharacteristics[$i][$key])
                if ($facialCharacteristics[$i][$key] > $max) {
                    $max = $facialCharacteristics[$i][$key];
                    $maxFrame = $i;
                }

//        return array(0 => $max, 1 => $maxFrame);
        return $max;
    }

    /**
     * Вычисление минимального значения характеристики лица за время наблюдений.
     *
     * @param $facialCharacteristics - массив с характеристикой лица
     * @param $key - название характеристики
     * @return array|bool - возвращаемое значение
     */
    public function getFaceDataMinForKey($facialCharacteristics, $key)
    {
        $facialCharacteristicsNumber = count($facialCharacteristics);
        if ($facialCharacteristicsNumber <= 0)
            return false;

        $min = $facialCharacteristics[0][$key];
        $minFrame = 0;

        for ($i = 0; $i < $facialCharacteristicsNumber; $i++)
            if ($facialCharacteristics[$i] && $facialCharacteristics[$i][$key])
                if ($facialCharacteristics[$i][$key] < $min) {
                    $min = $facialCharacteristics[$i][$key];
                    $minFrame = $i;
                }

//        return array(0 => $min, 1 => $minFrame);
        return $min;
    }

    /**
     * Обнаружение признаков глаза.
     *
     * @param $faceData - входной массив с лицевыми точками (landmarks)
     * @return mixed - выходной массив с обработанным массивом для глаза
     */
    public function detectEyeFeatures($faceData)
    {
       //for the right eye --------------------------------------------------------------------------
        $faceData["Characteristics"]["eye"]["right_eye_inner"]["MaxX"] = $this->getFaceDataMaxForKey(
            $faceData["eye"]["right_eye_inner"], "X");
        $faceData["Characteristics"]["eye"]["right_eye_inner"]["MaxY"] = $this->getFaceDataMaxForKey(
            $faceData["eye"]["right_eye_inner"], "Y");
        $faceData["Characteristics"]["eye"]["right_eye_inner"]["MinX"] = $this->getFaceDataMinForKey(
            $faceData["eye"]["right_eye_inner"], "X");
        $faceData["Characteristics"]["eye"]["right_eye_inner"]["MinY"] = $this->getFaceDataMinForKey(
            $faceData["eye"]["right_eye_inner"], "Y");
        // Здесь и далее опрделено экспертно на основе визуального анализа точек (кадры 115 и 119)
//        $faceData["Characteristics"]["eye"]["right_eye_inner"]["NatX"] = 422;
//        $faceData["Characteristics"]["eye"]["right_eye_inner"]["NatY"] = 250;
        if (isset($faceData["eye"]["right_eye_inner"][0])) {
            $faceData["Characteristics"]["eye"]["right_eye_inner"]["NatX"] = $faceData["eye"]["right_eye_inner"][0]['X'];
            $faceData["Characteristics"]["eye"]["right_eye_inner"]["NatY"] = $faceData["eye"]["right_eye_inner"][0]['Y'];
        }

        $faceData["Characteristics"]["eye"]["right_eye_outer"]["MaxX"] = $this->getFaceDataMaxForKey(
            $faceData["eye"]["right_eye_outer"], "X");
        $faceData["Characteristics"]["eye"]["right_eye_outer"]["MaxY"] = $this->getFaceDataMaxForKey(
            $faceData["eye"]["right_eye_outer"], "Y");
        $faceData["Characteristics"]["eye"]["right_eye_outer"]["MinX"] = $this->getFaceDataMinForKey(
            $faceData["eye"]["right_eye_outer"], "X");
        $faceData["Characteristics"]["eye"]["right_eye_outer"]["MinY"] = $this->getFaceDataMinForKey(
            $faceData["eye"]["right_eye_outer"], "Y");
//        $faceData["Characteristics"]["eye"]["right_eye_outer"]["NatX"] = 538;
//        $faceData["Characteristics"]["eye"]["right_eye_outer"]["NatY"] = 250;
        if (isset($faceData["eye"]["right_eye_outer"][0])) {
            $faceData["Characteristics"]["eye"]["right_eye_outer"]["NatX"] = $faceData["eye"]["right_eye_outer"][0]['X'];
            $faceData["Characteristics"]["eye"]["right_eye_outer"]["NatY"] = $faceData["eye"]["right_eye_outer"][0]['Y'];
        }

        $faceData["Characteristics"]["eye"]["right_upper_eyelid"]["MaxX"] = $this->getFaceDataMaxForKey(
            $faceData["eye"]["right_upper_eyelid"], "X");
        $faceData["Characteristics"]["eye"]["right_upper_eyelid"]["MaxY"] = $this->getFaceDataMaxForKey(
            $faceData["eye"]["right_upper_eyelid"], "Y");
        $faceData["Characteristics"]["eye"]["right_upper_eyelid"]["MinX"] = $this->getFaceDataMinForKey(
            $faceData["eye"]["right_upper_eyelid"], "X");
        $faceData["Characteristics"]["eye"]["right_upper_eyelid"]["MinY"] = $this->getFaceDataMinForKey(
            $faceData["eye"]["right_upper_eyelid"], "Y");
//        $faceData["Characteristics"]["eye"]["right_upper_eyelid"]["NatX"] = 505;
//        $faceData["Characteristics"]["eye"]["right_upper_eyelid"]["NatY"] = 232;
        if (isset($faceData["eye"]["right_upper_eyelid"][0])) {
            $faceData["Characteristics"]["eye"]["right_upper_eyelid"]["NatX"] = $faceData["eye"]["right_upper_eyelid"][0]['X'];
            $faceData["Characteristics"]["eye"]["right_upper_eyelid"]["NatY"] = $faceData["eye"]["right_upper_eyelid"][0]['Y'];
        }

        $faceData["Characteristics"]["eye"]["right_lower_eyelid"]["MaxX"] = $this->getFaceDataMaxForKey(
            $faceData["eye"]["right_lower_eyelid"], "X");
        $faceData["Characteristics"]["eye"]["right_lower_eyelid"]["MaxY"] = $this->getFaceDataMaxForKey(
            $faceData["eye"]["right_lower_eyelid"], "Y");
        $faceData["Characteristics"]["eye"]["right_lower_eyelid"]["MinX"] = $this->getFaceDataMinForKey(
            $faceData["eye"]["right_lower_eyelid"], "X");
        $faceData["Characteristics"]["eye"]["right_lower_eyelid"]["MinY"] = $this->getFaceDataMinForKey(
            $faceData["eye"]["right_lower_eyelid"], "Y");
//        $faceData["Characteristics"]["eye"]["right_lower_eyelid"]["MinYFrame"] = $this->faceDataMinForKey(
//            $faceData["eye"]["right_lower_eyelid"],"Y"
//        )[1];
//        $faceData["Characteristics"]["eye"]["right_lower_eyelid"]["NatX"] = 505;
//        $faceData["Characteristics"]["eye"]["right_lower_eyelid"]["NatY"] = 258;
        if (isset($faceData["eye"]["right_lower_eyelid"][0])) {
            $faceData["Characteristics"]["eye"]["right_lower_eyelid"]["NatX"] = $faceData["eye"]["right_lower_eyelid"][0]['X'];
            $faceData["Characteristics"]["eye"]["right_lower_eyelid"]["NatY"] = $faceData["eye"]["right_lower_eyelid"][0]['Y'];
        }

        // right_upper_eyelid - верхнее веко, движение верхнего века (N вверх, S вниз)
        $targetFaceData["eye"]["right_upper_eyelid"] = $this->moveY($faceData["eye"]["right_upper_eyelid"],
            $faceData["Characteristics"]["eye"]["right_upper_eyelid"]);
        // right_lower_eyelid - нижнее веко, движение нижнего века (X без движения, N вверх, S вниз)
        $targetFaceData["eye"]["right_lower_eyelid"] = $this->moveY($faceData["eye"]["right_lower_eyelid"],
            $faceData["Characteristics"]["eye"]["right_lower_eyelid"]);
        // right_eye_inner - внутренний уголок глаза, движение внутреннего уголка глаза (N вверх, S вниз)
        $targetFaceData["eye"]["right_eye_inner"] = $this->moveY($faceData["eye"]["right_eye_inner"],
            $faceData["Characteristics"]["eye"]["right_eye_inner"]);
        // right_eye_outer - внешний уголок глаза, движение внешнего уголка глаза (N вверх, S вниз)
        $targetFaceData["eye"]["right_eye_outer"] = $this->moveY($faceData["eye"]["right_eye_outer"],
            $faceData["Characteristics"]["eye"]["right_eye_outer"]);
        // right_eye_width - ширина глаз по Y ("+" увеличение ,"-" уменьшение)

        // Создание FaceFeature
//       $faceData["eye"]["right_eye_width"] = [];

        // Расчет Width для right_eye_width
        $faceData["eye"]["right_eye_width"] = $this->addOneDimDistance($faceData["eye"]["right_eye_width"], "Width",
            $faceData["eye"]["right_lower_eyelid"], "Y",
            $faceData["eye"]["right_upper_eyelid"], "Y");

        // Расчет характерисик для right_eye_width, сохранение характеристик
//       $rightEyeWidthNat = $this->getFaceDataAvrForKey($faceData["eye"]["right_eye_width"], "Width");
        if (isset($faceData["eye"]["right_eye_width"][0]))
         $rightEyeWidthNat = $this->$faceData["eye"]["right_eye_width"][0]["Width"];
        $rightEyeWidthMax = $this->getFaceDataMaxForKey($faceData["eye"]["right_eye_width"], "Width");
        $rightEyeWidthMin = $this->getFaceDataMinForKey($faceData["eye"]["right_eye_width"], "Width");

 //       $faceData["Characteristics"]["eye"]["right_eye_width"] = [];
        $faceData["Characteristics"]["eye"]["right_eye_width"]["Max"] = $rightEyeWidthMax;
        $faceData["Characteristics"]["eye"]["right_eye_width"]["Min"] = $rightEyeWidthMin;
        $faceData["Characteristics"]["eye"]["right_eye_width"]["Nat"] = $rightEyeWidthNat;

        // Расчет WidthChange и WidthChangeForce для right_eye_width
        $targetFaceData["eye"]["right_eye_width"] = $this->moveD($faceData["eye"]["right_eye_width"], "Width",
            $rightEyeWidthMax, $rightEyeWidthMin, $rightEyeWidthNat);

        //for the left eye -----------------------------------------------------
         $faceData["Characteristics"]["eye"]["left_eye_inner"]["MaxX"] = $this->getFaceDataMaxForKey(
            $faceData["eye"]["left_eye_inner"], "X");
        $faceData["Characteristics"]["eye"]["left_eye_inner"]["MaxY"] = $this->getFaceDataMaxForKey(
            $faceData["eye"]["left_eye_inner"], "Y");
        $faceData["Characteristics"]["eye"]["left_eye_inner"]["MinX"] = $this->getFaceDataMinForKey(
            $faceData["eye"]["left_eye_inner"], "X");
        $faceData["Characteristics"]["eye"]["left_eye_inner"]["MinY"] = $this->getFaceDataMinForKey(
            $faceData["eye"]["left_eye_inner"], "Y");
        // Здесь и далее опрделено экспертно на основе визуального анализа точек (кадры 115 и 119)
//        $faceData["Characteristics"]["eye"]["left_eye_inner"]["NatX"] = 422;
//        $faceData["Characteristics"]["eye"]["left_eye_inner"]["NatY"] = 250;
        if (isset($faceData["eye"]["left_eye_inner"][0])) {
            $faceData["Characteristics"]["eye"]["left_eye_inner"]["NatX"] = $faceData["eye"]["left_eye_inner"][0]['X'];
            $faceData["Characteristics"]["eye"]["left_eye_inner"]["NatY"] = $faceData["eye"]["left_eye_inner"][0]['Y'];
        }

        $faceData["Characteristics"]["eye"]["left_eye_outer"]["MaxX"] = $this->getFaceDataMaxForKey(
            $faceData["eye"]["left_eye_outer"], "X");
        $faceData["Characteristics"]["eye"]["left_eye_outer"]["MaxY"] = $this->getFaceDataMaxForKey(
            $faceData["eye"]["left_eye_outer"], "Y");
        $faceData["Characteristics"]["eye"]["left_eye_outer"]["MinX"] = $this->getFaceDataMinForKey(
            $faceData["eye"]["left_eye_outer"], "X");
        $faceData["Characteristics"]["eye"]["left_eye_outer"]["MinY"] = $this->getFaceDataMinForKey(
            $faceData["eye"]["left_eye_outer"], "Y");
//        $faceData["Characteristics"]["eye"]["left_eye_outer"]["NatX"] = 538;
//        $faceData["Characteristics"]["eye"]["left_eye_outer"]["NatY"] = 250;
        if (isset($faceData["eye"]["left_eye_outer"][0])) {
            $faceData["Characteristics"]["eye"]["left_eye_outer"]["NatX"] = $faceData["eye"]["left_eye_outer"][0]['X'];
            $faceData["Characteristics"]["eye"]["left_eye_outer"]["NatY"] = $faceData["eye"]["left_eye_outer"][0]['Y'];
        }

        $faceData["Characteristics"]["eye"]["left_upper_eyelid"]["MaxX"] = $this->getFaceDataMaxForKey(
            $faceData["eye"]["left_upper_eyelid"], "X");
        $faceData["Characteristics"]["eye"]["left_upper_eyelid"]["MaxY"] = $this->getFaceDataMaxForKey(
            $faceData["eye"]["left_upper_eyelid"], "Y");
        $faceData["Characteristics"]["eye"]["left_upper_eyelid"]["MinX"] = $this->getFaceDataMinForKey(
            $faceData["eye"]["left_upper_eyelid"], "X");
        $faceData["Characteristics"]["eye"]["left_upper_eyelid"]["MinY"] = $this->getFaceDataMinForKey(
            $faceData["eye"]["left_upper_eyelid"], "Y");
//        $faceData["Characteristics"]["eye"]["left_upper_eyelid"]["NatX"] = 505;
//        $faceData["Characteristics"]["eye"]["left_upper_eyelid"]["NatY"] = 232;
        if (isset($faceData["eye"]["left_upper_eyelid"][0])) {
            $faceData["Characteristics"]["eye"]["left_upper_eyelid"]["NatX"] = $faceData["eye"]["left_upper_eyelid"][0]['X'];
            $faceData["Characteristics"]["eye"]["left_upper_eyelid"]["NatY"] = $faceData["eye"]["left_upper_eyelid"][0]['Y'];
        }

        $faceData["Characteristics"]["eye"]["left_lower_eyelid"]["MaxX"] = $this->getFaceDataMaxForKey(
            $faceData["eye"]["left_lower_eyelid"], "X");
        $faceData["Characteristics"]["eye"]["left_lower_eyelid"]["MaxY"] = $this->getFaceDataMaxForKey(
            $faceData["eye"]["left_lower_eyelid"], "Y");
        $faceData["Characteristics"]["eye"]["left_lower_eyelid"]["MinX"] = $this->getFaceDataMinForKey(
            $faceData["eye"]["left_lower_eyelid"], "X");
        $faceData["Characteristics"]["eye"]["left_lower_eyelid"]["MinY"] = $this->getFaceDataMinForKey(
            $faceData["eye"]["left_lower_eyelid"], "Y");
//        $faceData["Characteristics"]["eye"]["right_lower_eyelid"]["MinYFrame"] = $this->faceDataMinForKey(
//            $faceData["eye"]["right_lower_eyelid"],"Y"
//        )[1];
        //!!!
        if (isset($faceData["eye"]["left_lower_eyelid"][0])) {
            $faceData["Characteristics"]["eye"]["left_lower_eyelid"]["NatX"] = $faceData["eye"]["left_lower_eyelid"][0]['X'];
            $faceData["Characteristics"]["eye"]["left_lower_eyelid"]["NatY"] = $faceData["eye"]["left_lower_eyelid"][0]['Y'];
        }
        // left_upper_eyelid - верхнее веко, движение верхнего века (N вверх, S вниз)
        $targetFaceData["eye"]["left_upper_eyelid"] = $this->moveY($faceData["eye"]["left_upper_eyelid"],
            $faceData["Characteristics"]["eye"]["left_upper_eyelid"]);
        // left_lower_eyelid - нижнее веко, движение нижнего века (X без движения, N вверх, S вниз)
        $targetFaceData["eye"]["left_lower_eyelid"] = $this->moveY($faceData["eye"]["left_lower_eyelid"],
            $faceData["Characteristics"]["eye"]["left_lower_eyelid"]);
        // left_eye_inner - внутренний уголок глаза, движение внутреннего уголка глаза (N вверх, S вниз)
        $targetFaceData["eye"]["left_eye_inner"] = $this->moveY($faceData["eye"]["left_eye_inner"],
            $faceData["Characteristics"]["eye"]["left_eye_inner"]);
        // left_eye_outer - внешний уголок глаза, движение внешнего уголка глаза (N вверх, S вниз)
        $targetFaceData["eye"]["left_eye_outer"] = $this->moveY($faceData["eye"]["left_eye_outer"],
            $faceData["Characteristics"]["eye"]["left_eye_outer"]);
        // left_eye_width - ширина глаз по Y ("+" увеличение ,"-" уменьшение)

        // Расчет Width для left_eye_width
        $faceData["eye"]["left_eye_width"] = $this->addOneDimDistance($faceData["eye"]["left_eye_width"],
            "Width",
            $faceData["eye"]["left_lower_eyelid"], "Y",
            $faceData["eye"]["left_upper_eyelid"], "Y");

        // Расчет характерисик для left_eye_width, сохранение характеристик
 //       $leftEyeWidthNat = $this->getFaceDataAvrForKey($faceData["eye"]["left_eye_width"], "Width");
        if (isset($faceData["eye"]["left_eye_width"][0]))
         $leftEyeWidthNat = $faceData["eye"]["left_eye_width"][0]['Width'];
        $leftEyeWidthMax = $this->getFaceDataMaxForKey($faceData["eye"]["left_eye_width"], "Width");
        $leftEyeWidthMin = $this->getFaceDataMinForKey($faceData["eye"]["left_eye_width"], "Width");

        //       $faceData["Characteristics"]["eye"]["right_eye_width"] = [];
        $faceData["Characteristics"]["eye"]["left_eye_width"]["Max"] = $leftEyeWidthMax;
        $faceData["Characteristics"]["eye"]["left_eye_width"]["Min"] = $leftEyeWidthMin;
        $faceData["Characteristics"]["eye"]["left_eye_width"]["Nat"] = $leftEyeWidthNat;

        // Расчет WidthChange и WidthChangeForce для right_eye_width
        $targetFaceData["eye"]["left_eye_width"] = $this->moveD($faceData["eye"]["left_eye_width"], "Width",
            $leftEyeWidthMax,  $leftEyeWidthMin, $leftEyeWidthNat);
        //-----------------------------------------------------------------------
        return $targetFaceData["eye"];
    }

    /**
     * конвертация входного файла И в массив АБ
     * @param $iFaceData - массив из json в формате И
     * @return array - массив в формате АБ
     */
    public function convertIJson($iFaceData)
    {
        $i = 0;
        foreach ($iFaceData as $k=>$v) {
            if (strpos(Trim($k), 'frame_') !== false){

              if(isset($v)) {
                  //norm points processing
                  if (isset($v['NORM_POINTS'])){
                  foreach ($v['NORM_POINTS'] as $k1 => $v1) {
//                for ($i1 = 0; $i1 < count($v['NORM_POINTS']); $i1++) {
//                   $pointName = $k1;
//                   echo $pointName.'<br>';
                      $FaceData_['normmask'][$i][$k1]['X'] = $v1[0];
                      $FaceData_['normmask'][$i][$k1]['Y'] = $v1[1];
                  }}
                  //brow points processing
                  if (isset($v['brow'])){
                  foreach ($v['brow'] as $k1 => $v1) {
                      $FaceData_['brow'][$i][$k1]['X'] = $v1[0];
                      $FaceData_['brow'][$i][$k1]['Y'] = $v1[1];
                  }}
                  //eyebrow points processing
                  if (isset($v['eyebrow'])){
                  foreach ($v['eyebrow'] as $k1 => $v1) {
                      $FaceData_['eyebrow'][$i][$k1]['X'] = $v1[0];
                      $FaceData_['eyebrow'][$i][$k1]['Y'] = $v1[1];
                  }}
                  //eye points processing
                  if (isset($v['eye'])){
                  foreach ($v['eye'] as $k1 => $v1) {
                      $FaceData_['eye'][$i][$k1]['X'] = $v1[0];
                      $FaceData_['eye'][$i][$k1]['Y'] = $v1[1];
                  }}
              }
             ++$i;
            }
        }
        return $FaceData_;
    }

    /**
     * Обнаружение признаков лба.
     *
     * @param $sourceFaceData - входной массив с лицевыми точками (landmarks)
     * @return array - выходной массив с обработанным массивом для лба
     */
    public function detectBrowFeatures($sourceFaceData){
     //для определения изменения ширины лба анализируем расстояние по Y между точками brow_center и left_eyebrow_center
     // right_eyebrow_center
        // получение нормированного значения по кадру 0
        if (isset($sourceFaceData['eyebrow']['left_eyebrow_center'][0])
            && isset($sourceFaceData['eyebrow']['right_eyebrow_center'][0])
            && isset($sourceFaceData['brow']['brow_center'][0])
        ) {

            $h1 = abs($sourceFaceData['eyebrow']['left_eyebrow_center'][0]['Y']-
                $sourceFaceData['brow']['brow_center'][0]['Y']);
            $h2 = abs($sourceFaceData['eyebrow']['right_eyebrow_center'][0]['Y']-
                $sourceFaceData['brow']['brow_center'][0]['Y']);
            $natH = round(($h1+$h2)/2); //среднее значение
        }
        $maxHForBrowCenter = $this->getFaceDataMaxForKey($sourceFaceData['brow']['brow_center'], "Y");
        $minHForBrowCenter = $this->getFaceDataMinForKey($sourceFaceData['brow']['brow_center'], "Y");
        $maxHForLeftEyebrowCenter = $this->getFaceDataMaxForKey($sourceFaceData['eyebrow']['left_eyebrow_center'], "Y");
        $minHForLeftEyebrowCenter = $this->getFaceDataMinForKey($sourceFaceData['eyebrow']['left_eyebrow_center'], "Y");
//        $maxHForRightEyebrowCenter = $this->getFaceDataMaxForKey($sourceFaceData['eyebrow']['right_eyebrow_center'], "Y");
//        $minHForRightEyebrowCenter = $this->getFaceDataMinForKey($sourceFaceData['eyebrow']['right_eyebrow_center'], "Y");
        $minH = abs($minHForLeftEyebrowCenter-$minHForBrowCenter);
        $maxH = abs($maxHForLeftEyebrowCenter-$maxHForBrowCenter);
        $scale = $maxH - $minH;

        for ($i = 0; $i < count($sourceFaceData['brow']['brow_center']); $i++) {
                $h1 = abs($sourceFaceData['eyebrow']['left_eyebrow_center'][$i]['Y']-
                    $sourceFaceData['brow']['brow_center'][$i]['Y']);
                $h2 = abs($sourceFaceData['eyebrow']['right_eyebrow_center'][$i]['Y']-
                    $sourceFaceData['brow']['brow_center'][$i]['Y']);
                $h = round(($h1+$h2)/2); //среднее значение

            $targetFaceData["brow"]["brow_width"][$i]["Force"] = $this->getForce(
                $scale, abs($h - $natH)
            );

            if ($h > $natH) $targetFaceData["brow"]["brow_width"][$i]["Val"] = '+';
            if ($h < $natH) $targetFaceData["brow"]["brow_width"][$i]["Val"] = '-';
            if ($h == $natH) $targetFaceData["brow"]["brow_width"][$i]["Val"] = 'none';


        }
      return $targetFaceData["brow"];
    }

    /**
     * Обнаружение признаков рта.
     *
     * @param $sourceFaceData - входной массив с лицевыми точками (landmarks)
     * @return array - выходной массив с обработанным массивом для глаза
     */
    public function detectMouthFeatures($sourceFaceData)
    {
        // first frame for standard (norm values)
        // get initial values
        // echo $FaceData_['normmask'][0][48][X];

        $facePoints = array(array(), array());
        for ($i = 0; $i < count($sourceFaceData['normmask'][0]); $i++)
            $facePoints[$i] = array(
                array($sourceFaceData['normmask'][0][$i]['X'], 500, 0, 0),
                array($sourceFaceData['normmask'][0][$i]['Y'], 500, 0, 0)
            );

        // get min and max values
        for ($i = 1; $i < count($sourceFaceData['normmask']); $i++)
            for ($j = 0; $j < count($sourceFaceData['normmask'][$i]); $j++) {

                // min
                // x
                // echo $facePoints[$j][0][1].' :: '.$FaceData_['frame_#'.$i]['NORM_POINTS'][$j][0].'<br>';
                if (($sourceFaceData['normmask'][$i][$j]['X'] < $facePoints[$j][0][1]) and
                    ($sourceFaceData['normmask'][$i][$j]['X'] != 0))
                    $facePoints[$j][0][1] = $sourceFaceData['normmask'][$i][$j]['X'];
                // y
                if (($sourceFaceData['normmask'][$i][$j]['Y'] < $facePoints[$j][1][1]) and
                    ($sourceFaceData['normmask'][$i][$j]['Y'] != 0))
                    $facePoints[$j][1][1] = $sourceFaceData['normmask'][$i][$j]['X'];

                // max
                // x
                if ($sourceFaceData['normmask'][$i][$j]['X'] > $facePoints[$j][0][2])
                    $facePoints[$j][0][2] = $sourceFaceData['normmask'][$i][$j]['X'];
                // y
                if ($sourceFaceData['normmask'][$i][$j]['Y'] > $facePoints[$j][1][2])
                    $facePoints[$j][1][2] = $sourceFaceData['normmask'][$i][$j]['Y'];
            }

        // get scale for x and y
        // length of the scale for power detection
        for ($i = 0; $i < count($facePoints); $i++) {
            $facePoints[$i][0][3] = $facePoints[$i][0][2] - $facePoints[$i][0][1];
            $facePoints[$i][1][3] = $facePoints[$i][1][2] - $facePoints[$i][1][1];
        }

        $targetFaceData = array();

        // print_r($facePoints);
        // изменнеие длины рта
        // NORM_POINTS 48 54
        // echo $FaceData_['normmask'][0][48][X];
        for ($i = 0; $i < count($sourceFaceData['normmask']); $i++) {
            if (isset($facePoints[48]) && isset($sourceFaceData['normmask'][$i][48])) {
                $x = abs($facePoints[48][0][0] - $sourceFaceData['normmask'][$i][48]['X']);
                $targetFaceData["mouth"]["left_corner_mouth"][$i]["Force"] = $this->getForce(
                    $facePoints[48][0][3], $x
                );
            }
            // echo $facePoints[48][0][0].'-'.$FaceData_['normmask'][$i][48]['X'].'='.
            // $x.' scale='.$facePoints[48][0][3].' force='.
            // $targetFaceData["mouth"]["left_corner_mouth"][$i]["MovmentForce"].'<br>';
            if (isset($targetFaceData["mouth"]["left_corner_mouth"][$i]))
                if ($targetFaceData["mouth"]["left_corner_mouth"][$i]["Force"] == 0)
                    $targetFaceData["mouth"]["left_corner_mouth"][$i]["MovmentDirection"] = 'none';
                else
                    if ($x > 0)
                        $targetFaceData["mouth"]["left_corner_mouth"][$i]["MovmentDirection"] = 'left';
                    else
                        $targetFaceData["mouth"]["left_corner_mouth"][$i]["MovmentDirection"] = 'right';

            if (isset($facePoints[54]) && isset($sourceFaceData['normmask'][$i][54]))
                $x = abs($facePoints[54][0][0] - $sourceFaceData['normmask'][$i][54]['X']);

            $targetFaceData["mouth"]["right_corner_mouth"][$i]["Force"] = $this->getForce(
                $facePoints[54][0][3], $x
            );

            if ($targetFaceData["mouth"]["right_corner_mouth"][$i]["Force"] == 0)
                $targetFaceData["mouth"]["right_corner_mouth"][$i]["MovmentDirection"] = 'none';
            else
                if ($x < 0)
                    $targetFaceData["mouth"]["right_corner_mouth"][$i]["MovmentDirection"] = 'left';
                else
                    $targetFaceData["mouth"]["right_corner_mouth"][$i]["MovmentDirection"] = 'right';
        }

        // изменение ширины рта
        // NORM_POINTS 51 57
        for ($i = 0; $i < count($sourceFaceData['normmask']); $i++) {
            if (isset($facePoints[51]) && isset($sourceFaceData['normmask'][$i][51])) {
                $y = abs($facePoints[51][1][0] - $sourceFaceData['normmask'][$i][51]['Y']);
                $targetFaceData["mouth"]["mouth_upper_lip_outer_center"][$i]["Force"] = $this->getForce(
                    $facePoints[51][1][3], $y
                );
            }

            if (isset($targetFaceData["mouth"]["left_corner_mouth"][$i]))
                if ($targetFaceData["mouth"]["mouth_upper_lip_outer_center"][$i]["Force"] == 0)
                    $targetFaceData["mouth"]["mouth_upper_lip_outer_center"][$i]["MovmentDirection"] = 'none';
                else
                    if ($y > 0)
                        $targetFaceData["mouth"]["mouth_upper_lip_outer_center"][$i]["MovmentDirection"] = 'up';
                    else
                        $targetFaceData["mouth"]["mouth_upper_lip_outer_center"][$i]["MovmentDirection"] = 'down';

            if (isset($facePoints[57]) && isset($sourceFaceData['normmask'][$i][57]))
                $y = abs($facePoints[57][1][0] - $sourceFaceData['normmask'][$i][57]['Y']);

            $targetFaceData["mouth"]["mouth_lower_lip_outer_center"][$i]["Force"] =
                $this->getForce($facePoints[57][0][3], $y);

            if ($targetFaceData["mouth"]["mouth_lower_lip_outer_center"][$i]["Force"] == 0)
                $targetFaceData["mouth"]["mouth_lower_lip_outer_center"][$i]["MovmentDirection"] = 'none';
            else
                if ($y < 0)
                    $targetFaceData["mouth"]["mouth_lower_lip_outer_center"][$i]["MovmentDirection"] = 'down';
                else
                    $targetFaceData["mouth"]["mouth_lower_lip_outer_center"][$i]["MovmentDirection"] = 'up';
        }

        // определение формы рта
        // NORM_POINTS 61 62 63 65 66 67
        for ($i = 0; $i < count($sourceFaceData['normmask']); $i++) {
            if (isset($sourceFaceData['normmask'][$i][67])) {
                $width1 = $sourceFaceData['normmask'][$i][67]['Y'] - $sourceFaceData['normmask'][$i][61]['Y'];
                $width2 = $sourceFaceData['normmask'][$i][66]['Y'] - $sourceFaceData['normmask'][$i][62]['Y'];
                $width3 = $sourceFaceData['normmask'][$i][65]['Y'] - $sourceFaceData['normmask'][$i][63]['Y'];
                $lengthTest = $sourceFaceData['normmask'][$i][65]['X'] - $sourceFaceData['normmask'][$i][67]['X'];
            }
            // echo $width1.'/'.$width2.'/'.$width3.'<br>';
            if (($width1 != 0) and ($width2 != 0) and ($width3 != 0) and ($lengthTest / 4 < $width2))
                if (($width1 < $width2) and ($width3 < $width2))
                    $targetFaceData["mouth"]["mouth_form"][$i]["Val"] = 'ellipse';
                else
                    $targetFaceData["mouth"]["mouth_form"][$i]["Val"] = 'rectangle';
            else
                $targetFaceData["mouth"]["mouth_form"][$i]["Val"] = 'line';
        }

        // движение уголков рта
        // NORM_POINTS 48 54
        for ($i = 0; $i < count($sourceFaceData['normmask']); $i++) {
            if (isset($facePoints[48]) && isset($sourceFaceData['normmask'][$i][48]))
                $y = abs($facePoints[48][1][0] - $sourceFaceData['normmask'][$i][48]['Y']);

            $leftCornerMouthMovementForce = $this->getForce($facePoints[48][1][3], $y);

            if (isset($facePoints[54]) && isset($sourceFaceData['normmask'][$i][54]))
                $y1 = abs($facePoints[54][1][0] - $sourceFaceData['normmask'][$i][54]['Y']);

            $rightCornerMouthMovementForce = $this->getForce($facePoints[54][1][3], $y1);

            // get min force
            if ($leftCornerMouthMovementForce > $rightCornerMouthMovementForce)
                $leftCornerMouthMovementForce=$rightCornerMouthMovementForce;

            $targetFaceData["mouth"]["mouth_corner_movement"][$i]["Force"] = $leftCornerMouthMovementForce;

            if (($rightCornerMouthMovementForce == 0) or ($leftCornerMouthMovementForce == 0))
                $targetFaceData["mouth"]["mouth_corner_movement"][$i]["MovmentDirection"] = 'none';
            else
                if (($y > 0) and ($y1 > 0))
                    $targetFaceData["mouth"]["mouth_corner_movement"][$i]["MovmentDirection"] = 'up';
                else
                    $targetFaceData["mouth"]["mouth_corner_movement"][$i]["MovmentDirection"] = 'down';
        }

        // движение уголков рта NORM_POINTS 48 54
        return $targetFaceData["mouth"];
    }

    /**
     * Обнаружение трендов (универсальная функция)
     *
     * @param $sourceFaceData - входной массив с лицевыми точками (landmarks)
     * @return array - выходной массив с обработанным массивом
     */
    public function detectTrends($sourceFaceData1, $trendLength)
    {
        foreach ($sourceFaceData1 as $k=>$v) {
            foreach ($v as $k1=>$v1) {
                if(isset($v1[0])) $arrayKeys = array_keys($v1[0]);
                $currentTrendLength = 0;
                for ($i = 1; $i < count($v1); $i++) {
                    if(isset($arrayKeys[1])) {
                        $val0 = $v1[$i-1][$arrayKeys[1]];
                        $val1 = $v1[$i][$arrayKeys[1]];
                    }
                    if (($v1[$i]["Force"] != 0)//force не рабно нулю
                        and ($val0 == $val1)) { //значение не меняет направление
                        $currentTrendLength++;
                        $v1[$i]["Trend"] = $currentTrendLength;
                        $v1[$i]["Confidence"] = 1;
                    } else { //the trend is change direction or force = 0
//                        if ($currentTrendLength < $trendLength) {
//                            echo $currentTrendLength . ' ' . $i . '<br>';
                            //clear features of previouse frames
                        $v1[$i]["Trend"] = 0;
                        $v1[$i]["Confidence"] = 0;
                        for ($i1 = $i; $i1 < ($i - $currentTrendLength); $i1--) {
                                $v1[$i1]["Confidence"] = 0;
//                            if (isset($v[$i1][1])) $v[$i1][1] = 'none';
                            }
                            $currentTrendLength = 0;
 //                       }
                    }
                }
             $sourceFaceData1[$k][$k1] = $v1;
            }
        }
        return $sourceFaceData1;
    }

    /**
     * Обнаружение признаков на основе анализа входных данных
     *
     * @param $sourceFile - входной файл в формате json с лицевыми точками (landmarks)
     * @return array - выходной массив с опредеделенными признаками
     */
    public function detectFeatures($sourceFile)
    {
        //load data
        $json = file_get_contents($sourceFile, true);
        $FaceData_=json_decode($json, true);
        //check input format
        //convert the I format to AB
        if(strpos($json,'NORM_POINTS') !== false){
         $FaceData = $this->convertIJson($FaceData_);
        } else{
            //use the AB format
            $FaceData =  $FaceData_;
        }
        $detectedFeatures['eye'] = $this->detectEyeFeatures($FaceData);
        $detectedFeatures['mouth'] = $this->detectMouthFeatures($FaceData);
        $detectedFeatures['brow'] = $this->detectBrowFeatures($FaceData);
        $detectedFeaturesWithTrends = $this->detectTrends($detectedFeatures,5);
     return $detectedFeaturesWithTrends;
    }
}