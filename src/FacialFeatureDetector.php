<?php

/**
 * FacialFeatureDetector - класс обнаружения лицевых признаков.
 */
class FacialFeatureDetector
{
    /**
     * Вычисляет новую характеристику с именем $newFacialCharacteristicsName в массиве $facialCharacteristics
     * с учетом разбиений на фреймы.
     *
     * @param $facialCharacteristics - массив харрактеристик лица
     * @param $newFacialCharacteristicsName - название новой харрактеристики лица
     * @param $facialCharacteristics1
     * @param $key1
     * @param $facialCharacteristics2
     * @param $key2
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
            $facialCharacteristics[] = array();
            if ($facialCharacteristics1[$i] && $facialCharacteristics1[$i][$key1] &&
                $facialCharacteristics2[$i] && $facialCharacteristics2[$i][$key2])
                $facialCharacteristics[$i][$newFacialCharacteristicsName] = $facialCharacteristics1[$i][$key1] -
                    $facialCharacteristics2[$i][$key2];
        }

        return true;
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
    public function dMove($facialCharacteristics, $key, $max, $min, $nat)
    {
        $facialCharacteristicsNumber = count($facialCharacteristics);
        if ($facialCharacteristicsNumber <= 0)
            return false;

        $deltaForMinus = $min - $nat;
        $deltaForPlus = $max - $nat;

        for ($i = 0; $i < $facialCharacteristicsNumber; $i++) {
            if ($facialCharacteristics[$i] && $facialCharacteristics[$i][$key]) {
                if ($facialCharacteristics[$i][$key] < $nat) {
                    // Уменьшение ширины
                    $facialCharacteristics[$i]["WidthChange"] = "-";
                    $facialCharacteristics[$i]["WidthChangeForce"] = round(
                        (($facialCharacteristics[$i][$key] - $nat) / $deltaForMinus),
                        2
                    );
                } elseif ($facialCharacteristics[$i][$key] > $nat) {
                    // Увеличение ширины
                    $facialCharacteristics[$i]["WidthChange"] = "+";
                    $facialCharacteristics[$i]["WidthChangeForce"] = round(
                        (($facialCharacteristics[$i][$key] - $nat) / $deltaForPlus),
                        2
                    );
                } else {
                    // Ввести погрешность для определения отсутсвтия движения
                    $facialCharacteristics[$i]["WidthChange"] = "X";
                    $facialCharacteristics[$i]["WidthChangeForce"] = 0;
                }
            }
        }

        return true;
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
    public function yMove($facialLandmarkCharacteristics, $characteristics)
    {
        $facialLandmarkCharacteristicsNumber = count($facialLandmarkCharacteristics);
        if ($facialLandmarkCharacteristicsNumber <= 0)
            return false;

        $deltaForN = $characteristics["MinY"] - $characteristics["NatY"];
        $deltaForS = $characteristics["MaxY"] - $characteristics["NatY"];

        for ($i = 0; $i < $facialLandmarkCharacteristicsNumber; $i++) {
            if ($facialLandmarkCharacteristics[$i] && $facialLandmarkCharacteristics[$i]["Y"]) {
                if ($facialLandmarkCharacteristics[$i]["Y"] < $characteristics["NatY"]) {
                    $facialLandmarkCharacteristics[$i]["MovementDirection"] = "N";
                    $facialLandmarkCharacteristics[$i]["MovementForce"] = round(
                        (($facialLandmarkCharacteristics[$i]["Y"] - $characteristics["NatY"]) / $deltaForN),
                        2
                    );
                } elseif ($facialLandmarkCharacteristics[$i]["Y"] > $characteristics["NatY"]) {
                    $facialLandmarkCharacteristics[$i]["MovementDirection"] = "S";
                    $facialLandmarkCharacteristics[$i]["MovementForce"] = round(
                        (($facialLandmarkCharacteristics[$i]["Y"] - $characteristics["NatY"]) / $deltaForS),
                        2
                    );
                } else {
                    // Ввести погрешность для определения отсутсвтия движения
                    $facialLandmarkCharacteristics[$i]["MovementDirection"] = "X";
                    $facialLandmarkCharacteristics[$i]["MovementForce"] = 0;
                }
            }
        }

        return true;
    }

    /**
     * Вычисление среднего значения характеристики лица за время наблюдений.
     *
     * @param $facialCharacteristics - массив с характеристикой лица
     * @param $key - название характеристики
     * @return float|bool - возвращаемое значение
     */
    public function faceDataAvrForKey($facialCharacteristics, $key)
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
    public function faceDataMaxForKey($facialCharacteristics, $key)
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

        return array(0 => $max, 1 => $maxFrame);
    }

    /**
     * Вычисление минимального значения характеристики лица за время наблюдений.
     *
     * @param $facialCharacteristics - массив с характеристикой лица
     * @param $key - название характеристики
     * @return array|bool - возвращаемое значение
     */
    public function faceDataMinForKey($facialCharacteristics, $key)
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

        return array(0 => $min, 1 => $minFrame);
    }

    /**
     * Обнаружение признаков глаза.
     *
     * @param $faceData - входной массив с лицевыми точками (landmarks)
     * @return mixed - выходной массив с обработанным массивом для глаза
     */
    public function eyeFeaturesDetection($faceData)
    {
        $faceData["Characteristics"]["eye"]["right_eye_inner"]["MaxX"] = $this->faceDataMaxForKey(
            $faceData["eye"]["right_eye_inner"], "X"
        )[0];
        $faceData["Characteristics"]["eye"]["right_eye_inner"]["MaxY"] = $this->faceDataMaxForKey(
            $faceData["eye"]["right_eye_inner"], "Y"
        )[0];
        $faceData["Characteristics"]["eye"]["right_eye_inner"]["MinX"] = $this->faceDataMinForKey(
            $faceData["eye"]["right_eye_inner"], "X"
        )[0];
        $faceData["Characteristics"]["eye"]["right_eye_inner"]["MinY"] = $this->faceDataMinForKey(
            $faceData["eye"]["right_eye_inner"], "Y"
        )[0];
        // Здесь и далее опрделено экспертно на основе визуального анализа точек (кадры 115 и 119)
        $faceData["Characteristics"]["eye"]["right_eye_inner"]["NatX"] = 422;
        $faceData["Characteristics"]["eye"]["right_eye_inner"]["NatY"] = 250;

        $faceData["Characteristics"]["eye"]["right_eye_outer"]["MaxX"] = $this->faceDataMaxForKey(
            $faceData["eye"]["right_eye_outer"], "X"
        )[0];
        $faceData["Characteristics"]["eye"]["right_eye_outer"]["MaxY"] = $this->faceDataMaxForKey(
            $faceData["eye"]["right_eye_outer"], "Y"
        )[0];
        $faceData["Characteristics"]["eye"]["right_eye_outer"]["MinX"] = $this->faceDataMinForKey(
            $faceData["eye"]["right_eye_outer"], "X"
        )[0];
        $faceData["Characteristics"]["eye"]["right_eye_outer"]["MinY"] = $this->faceDataMinForKey(
            $faceData["eye"]["right_eye_outer"], "Y"
        )[0];
        $faceData["Characteristics"]["eye"]["right_eye_outer"]["NatX"] = 538;
        $faceData["Characteristics"]["eye"]["right_eye_outer"]["NatY"] = 250;

        $faceData["Characteristics"]["eye"]["right_upper_eyelid"]["MaxX"] = $this->faceDataMaxForKey(
            $faceData["eye"]["right_upper_eyelid"], "X"
        )[0];
        $faceData["Characteristics"]["eye"]["right_upper_eyelid"]["MaxY"] = $this->faceDataMaxForKey(
            $faceData["eye"]["right_upper_eyelid"], "Y"
        )[0];
        $faceData["Characteristics"]["eye"]["right_upper_eyelid"]["MinX"] = $this->faceDataMinForKey(
            $faceData["eye"]["right_upper_eyelid"], "X"
        )[0];
        $faceData["Characteristics"]["eye"]["right_upper_eyelid"]["MinY"] = $this->faceDataMinForKey(
            $faceData["eye"]["right_upper_eyelid"], "Y"
        )[0];
        $faceData["Characteristics"]["eye"]["right_upper_eyelid"]["NatX"] = 505;
        $faceData["Characteristics"]["eye"]["right_upper_eyelid"]["NatY"] = 232;

        $faceData["Characteristics"]["eye"]["right_lower_eyelid"]["MaxX"] = $this->faceDataMaxForKey(
            $faceData["eye"]["right_lower_eyelid"], "X"
        )[0];
        $faceData["Characteristics"]["eye"]["right_lower_eyelid"]["MaxY"] = $this->faceDataMaxForKey(
            $faceData["eye"]["right_lower_eyelid"], "Y"
        )[0];
        $faceData["Characteristics"]["eye"]["right_lower_eyelid"]["MinX"] = $this->faceDataMinForKey(
            $faceData["eye"]["right_lower_eyelid"], "X"
        )[0];
        $faceData["Characteristics"]["eye"]["right_lower_eyelid"]["MinY"] = $this->faceDataMinForKey(
            $faceData["eye"]["right_lower_eyelid"], "Y"
        )[0];
//        $faceData["Characteristics"]["eye"]["right_lower_eyelid"]["MinYFrame"] = $this->faceDataMinForKey(
//            $faceData["eye"]["right_lower_eyelid"],"Y"
//        )[1];
        $faceData["Characteristics"]["eye"]["right_lower_eyelid"]["NatX"] = 505;
        $faceData["Characteristics"]["eye"]["right_lower_eyelid"]["NatY"] = 258;

        // Всё сделано только для правого глаза
        // right_upper_eyelid - верхнее веко, движение верхнего века (N вверх, S вниз)
        $this->yMove($faceData["eye"]["right_upper_eyelid"], $faceData["Characteristics"]["eye"]["right_upper_eyelid"]);
        // right_lower_eyelid - нижнее веко, движение нижнего века (X без движения, N вверх, S вниз)
        $this->yMove($faceData["eye"]["right_lower_eyelid"], $faceData["Characteristics"]["eye"]["right_lower_eyelid"]);
        // right_eye_inner - внутренний уголок глаза, движение внутреннего уголка глаза (N вверх, S вниз)
        $this->yMove($faceData["eye"]["right_eye_inner"], $faceData["Characteristics"]["eye"]["right_eye_inner"]);
        // right_eye_outer - внешний уголок глаза, движение внешнего уголка глаза (N вверх, S вниз)
        $this->yMove($faceData["eye"]["right_eye_outer"], $faceData["Characteristics"]["eye"]["right_eye_outer"]);
        // right_eye_width - ширина глаз по Y ("+" увеличение ,"-" уменьшение)

        // Создание FaceFeature
        $faceData["eye"]["right_eye_width"] = [];

        // Расчет Width для right_eye_width
        $this->addOneDimDistance($faceData["eye"]["right_eye_width"], "Width",
            $faceData["eye"]["right_lower_eyelid"], "Y",
            $faceData["eye"]["right_upper_eyelid"], "Y");

        // Расчет характерисик для right_eye_width, сохранение характеристик
        $right_eye_width_nat = $this->faceDataAvrForKey($faceData["eye"]["right_eye_width"], "Width");
        $right_eye_width_max = $this->faceDataMaxForKey($faceData["eye"]["right_eye_width"], "Width")[0];
        $right_eye_width_min = $this->faceDataMinForKey($faceData["eye"]["right_eye_width"], "Width")[0];

        $faceData["Characteristics"]["eye"]["right_eye_width"] = [];
        $faceData["Characteristics"]["eye"]["right_eye_width"]["Max"] = $right_eye_width_max;
        $faceData["Characteristics"]["eye"]["right_eye_width"]["Min"] = $right_eye_width_min;
        $faceData["Characteristics"]["eye"]["right_eye_width"]["Nat"] = $right_eye_width_nat;

        // Расчет WidthChange и WidthChangeForce для right_eye_width
        $this->dMove($faceData["eye"]["right_eye_width"], "Width", $right_eye_width_max,
            $right_eye_width_min, $right_eye_width_nat);

        return $faceData["eye"];
    }

    /**
     * @param $val1
     * @param $val2
     * @return float|int
     */
    public function getForce($val1, $val2)
    {
        $af = $val1 / 5;
        $res = abs(round($val2 / $af));

        return $res;
    }

    public function mouthFeatureDetection($faceData_)
    {
        // first frame for standard (norm values)
        // get initial values
        //echo $FaceData_['normmask'][0][48][X];

        $facePoints = array(array(), array());
        for ($ii = 0; $ii < count($faceData_['normmask'][0]); $ii++)
            $facePoints[$ii] = array(
                array($faceData_['normmask'][0][$ii]['X'], 500, 0, 0),
                array($faceData_['normmask'][0][$ii]['Y'], 500, 0, 0)
            );

        // get min and max values
        for ($i = 1; $i < count($faceData_['normmask']); $i++)
            for ($ii = 0; $ii < count($faceData_['normmask'][$i]); $ii++) {
                // min
                // x
                // echo $facePoints[$ii][0][1].' :: '.$FaceData_['frame_#'.$i]['NORM_POINTS'][$ii][0].'<br>';
                if (($faceData_['normmask'][$i][$ii]['X'] < $facePoints[$ii][0][1]) and
                    ($faceData_['normmask'][$i][$ii]['X'] != 0))
                    $facePoints[$ii][0][1] = $faceData_['normmask'][$i][$ii]['X'];
                //y
                if (($faceData_['normmask'][$i][$ii]['Y'] < $facePoints[$ii][1][1]) and
                    ($faceData_['normmask'][$i][$ii]['Y'] != 0))
                    $facePoints[$ii][1][1] = $faceData_['normmask'][$i][$ii]['X'];
                // max
                // x
                if ($faceData_['normmask'][$i][$ii]['X'] > $facePoints[$ii][0][2])
                    $facePoints[$ii][0][2] = $faceData_['normmask'][$i][$ii]['X'];
                // y
                if ($faceData_['normmask'][$i][$ii]['Y'] > $facePoints[$ii][1][2])
                    $facePoints[$ii][1][2] = $faceData_['normmask'][$i][$ii]['Y'];
            }

        // get scale for x and y
        // lenght of the scale for power detection
        for ($i = 0; $i < count($facePoints); $i++) {
            $facePoints[$i][0][3] = $facePoints[$i][0][2] - $facePoints[$i][0][1];
            $facePoints[$i][1][3] = $facePoints[$i][1][2] - $facePoints[$i][1][1];
        }

        $faceData = array();

        // print_r($facePoints);
        // изменнеие длины рта
        // NORM_POINTS 48 54
        // echo $FaceData_['normmask'][0][48][X];
        for ($i = 0; $i < count($faceData_['normmask']); $i++) {
            if (isset($facePoints[48]) && isset($faceData_['normmask'][$i][48])) {
                $x = abs($facePoints[48][0][0] - $faceData_['normmask'][$i][48]['X']);
                $faceData["mouth"]["left_corner_mouth"][$i]["MovmentForce"] = $this->getForce($facePoints[48][0][3], $x);
            }
            // echo $facePoints[48][0][0].'-'.$FaceData_['normmask'][$i][48]['X'].'='.
            // $x.' scale='.$facePoints[48][0][3].' force='.
            // $faceData["mouth"]["left_corner_mouth"][$i]["MovmentForce"].'<br>';
            if (isset($faceData["mouth"]["left_corner_mouth"][$i]))
                if ($faceData["mouth"]["left_corner_mouth"][$i]["MovmentForce"] == 0)
                    $faceData["mouth"]["left_corner_mouth"][$i]["MovmentDirection"] = 'none';
                else
                    if ($x > 0)
                        $faceData["mouth"]["left_corner_mouth"][$i]["MovmentDirection"] = 'left';
                    else
                        $faceData["mouth"]["left_corner_mouth"][$i]["MovmentDirection"] = 'right';

            if (isset($facePoints[54]) && isset($faceData_['normmask'][$i][54]))
                $x = abs($facePoints[54][0][0] - $faceData_['normmask'][$i][54]['X']);

            $faceData["mouth"]["right_corner_mouth"][$i]["MovmentForce"] = $this->getForce($facePoints[54][0][3], $x);

            if ($faceData["mouth"]["right_corner_mouth"][$i]["MovmentForce"] == 0)
                $faceData["mouth"]["right_corner_mouth"][$i]["MovmentDirection"]='none';
            else
                if ($x < 0)
                    $faceData["mouth"]["right_corner_mouth"][$i]["MovmentDirection"]='left';
                else
                    $faceData["mouth"]["right_corner_mouth"][$i]["MovmentDirection"]='right';
        }

        // изменение ширины рта
        // NORM_POINTS 51 57
        for ($i = 0; $i < count($faceData_['normmask']); $i++) {
            if (isset($facePoints[51]) && isset($faceData_['normmask'][$i][51])) {
                $y = abs($facePoints[51][1][0] - $faceData_['normmask'][$i][51]['Y']);
                $faceData["mouth"]["mouth_upper_lip_outer_center"][$i]["MovmentForce"] = $this->getForce(
                    $facePoints[51][1][3], $y
                );
            }

            if (isset($faceData["mouth"]["left_corner_mouth"][$i]))
                if ($faceData["mouth"]["mouth_upper_lip_outer_center"][$i]["MovmentForce"] == 0)
                    $faceData["mouth"]["mouth_upper_lip_outer_center"][$i]["MovmentDirection"] = 'none';
                else
                    if ($y > 0)
                        $faceData["mouth"]["mouth_upper_lip_outer_center"][$i]["MovmentDirection"] = 'up';
                    else
                        $faceData["mouth"]["mouth_upper_lip_outer_center"][$i]["MovmentDirection"] = 'down';

            if (isset($facePoints[57]) && isset($faceData_['normmask'][$i][57]))
                $y = abs($facePoints[57][1][0] - $faceData_['normmask'][$i][57]['Y']);

            $faceData["mouth"]["mouth_lower_lip_outer_center"][$i]["MovmentForce"] =
                $this->getForce($facePoints[57][0][3], $y);

            if ($faceData["mouth"]["mouth_lower_lip_outer_center"][$i]["MovmentForce"] == 0)
                $faceData["mouth"]["mouth_lower_lip_outer_center"][$i]["MovmentDirection"] = 'none';
            else
                if ($y < 0)
                    $faceData["mouth"]["mouth_lower_lip_outer_center"][$i]["MovmentDirection"] = 'down';
                else
                    $faceData["mouth"]["mouth_lower_lip_outer_center"][$i]["MovmentDirection"] = 'up';
        }

        // определение формы рта
        // NORM_POINTS 61 62 63 65 66 67
        for ($i = 0; $i < count($faceData_['normmask']); $i++) {
            if (isset($faceData_['normmask'][$i][67])) {
                $width1 = $faceData_['normmask'][$i][67]['Y'] - $faceData_['normmask'][$i][61]['Y'];
                $width2 = $faceData_['normmask'][$i][66]['Y'] - $faceData_['normmask'][$i][62]['Y'];
                $width3 = $faceData_['normmask'][$i][65]['Y'] - $faceData_['normmask'][$i][63]['Y'];
                $length_test = $faceData_['normmask'][$i][65]['X'] - $faceData_['normmask'][$i][67]['X'];
            }
            // echo $width1.'/'.$width2.'/'.$width3.'<br>';
            if (($width1 != 0) and ($width2 != 0) and ($width3 != 0) and ($length_test / 4 < $width2))
                if (($width1 < $width2) and ($width3 < $width2))
                    $faceData["mouth"]["mouth_form"][$i]["Val"] = 'ellipse';
                else
                    $faceData["mouth"]["mouth_form"][$i]["Val"] = 'rectangle';
            else
                $faceData["mouth"]["mouth_form"][$i]["Val"] = 'line';
        }

        // движение уголков рта
        // NORM_POINTS 48 54
        for ($i = 0; $i < count($faceData_['normmask']); $i++) {
            if (isset($facePoints[48]) && isset($faceData_['normmask'][$i][48]))
                $y = abs($facePoints[48][1][0] - $faceData_['normmask'][$i][48]['Y']);

            $leftCornerMouthMovementForce = $this->getForce($facePoints[48][1][3], $y);

            if (isset($facePoints[54]) && isset($faceData_['normmask'][$i][54]))
                $y1 = abs($facePoints[54][1][0] - $faceData_['normmask'][$i][54]['Y']);

            $rightCornerMouthMovementForce = $this->getForce($facePoints[54][1][3], $y1);

            // get min force
            if ($leftCornerMouthMovementForce > $rightCornerMouthMovementForce)
                $leftCornerMouthMovementForce=$rightCornerMouthMovementForce;

            $faceData["mouth"]["mouth_corner_movement"][$i]["MovmentForce"] = $leftCornerMouthMovementForce;

            if (($rightCornerMouthMovementForce == 0) or ($leftCornerMouthMovementForce == 0))
                $faceData["mouth"]["mouth_corner_movement"][$i]["MovmentDirection"] = 'none';
            else
                if (($y > 0) and ($y1 > 0))
                    $faceData["mouth"]["mouth_corner_movement"][$i]["MovmentDirection"] = 'up';
                else
                    $faceData["mouth"]["mouth_corner_movement"][$i]["MovmentDirection"] = 'down';
        }

        // движение уголков рта NORM_POINTS 48 54
        return $faceData;
    }
}