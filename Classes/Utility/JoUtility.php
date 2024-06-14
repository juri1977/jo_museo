<?php
namespace JO\JoMuseo\Utility;

class JoUtility
{

    public static function mkTimestamp($string, $format = 'd.m.Y')
    {
        $timestamp = 0;

        $date = \DateTime::createFromFormat($format, $string);
        $timestamp = date_format($date, 'U');

        return $timestamp;
    }

    public static function addPyramidZindexToArray($arr, $subArrIndex = null)
    {
        $anz = count($arr);
        $maxCounter = $anz / 2;
        $index = 0;
        $zIndex = 0;
        // css z-index um eins erhöhen bis index größer als die hälfte dann wieder runterzählen
        if (!empty($anz) && $anz > 1) {
            foreach ($arr as $key => $value) {
                if ($index < $maxCounter) {
                    $zIndex++;
                } else {
                    $zIndex--;
                }
                if (null != $subArrIndex) {
                    $arr[$key]['sliderConfiguration']['zIndex'] = $zIndex;
                } else {
                    $arr[$key]['zIndex'] = $zIndex;
                }
                $index++;
            }
        }
        return $arr;
    }
}
