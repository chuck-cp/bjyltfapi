<?php
/*
 * 小工具类
 * */
namespace common\libs;
use Yii;
use yii\base\Exception;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Cookie;
use yii\web\UploadedFile;

class ArrayClass
{
    /*
     * 根据数组的key排序
     * @param array array 元数据
     * @param string key 要排序的key值
     * @param string order 正序或到序(如果是数组则按照数组的规则排序)
     * */
    public static function sort($array,$key,$order='asc'){
        if(empty($array)) return false;
        $arrayLen = count($array);
        if($order == 'asc'){
            for($i = 0;$i < $arrayLen;$i++){
                for($j = $i + 1;$j < $arrayLen;$j++){
                    if($array[$i][$key] > $array[$j][$key]){
                        $temp = $array[$i];
                        $array[$i] = $array[$j];
                        $array[$j] = $temp;
                    }
                }
            }
        }else{
            for($i = 0;$i < $arrayLen;$i++){
                for($j = $i + 1;$j < $arrayLen;$j++){
                    if($array[$i][$key] < $array[$j][$key]){
                        $temp = $array[$i];
                        $array[$i] = $array[$j];
                        $array[$j] = $temp;
                    }
                }
            }
        }
        return $array;
    }
}
