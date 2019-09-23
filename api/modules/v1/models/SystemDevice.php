<?php

namespace api\modules\v1\models;

use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "{{%system_account}}".
 *
 * @property integer $total
 * @property integer $adv_expend
 * @property integer $margin
 */
class SystemDevice extends \api\core\ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%system_device}}';
    }


    /**
     * 验证设备是否合法
     * @param $device_numbers
     * @return bool
     */
    public static function checkIsOut($software_number){
        if(!is_array($software_number)){
            return false;
        }
        $device_numbers = [];
        foreach ($software_number as $k => $v){
            $exist = self::find()->where(['software_id'=>$v, 'status'=>1, 'is_output'=>1])->select('device_number')->asArray()->one();
            if(empty($exist)){ return false; }
            //硬件
            $device_numbers[$k]['realNum'] = $exist['device_number'];
            //软件
            $device_numbers[$k]['deviceNum'] = $v;
            $device_numbers[$k]['size'] = '0';
        }
        return $device_numbers;
    }
    //硬件编号找软件编号
    public static function getSolf($device_id){
        if(is_string($device_id)){
            $obj = self::find()->where(['device_number'=>$device_id])->select('software_id')->asArray()->one();
            if(!empty($obj)){
                return $obj['software_id'];
            }
            return false;
        }
        if(is_array($device_id)){
            $deviceArr = [];
            foreach ($device_id as $k => $v){
                $deviceArr[] = self::getSolf($v);
            }
            return $deviceArr;
        }
        return false;
    }
    //软件编号获取硬件编号
    public static function getDevice($soft_id){
        if(is_string($soft_id)){
            $obj = self::find()->where(['software_id'=>$soft_id, 'status'=>1, 'is_output'=>1])->select('device_number')->asArray()->one();
            if(!empty($obj)){
                return $obj['device_number'];
            }
            return false;
        }
        if(is_array($soft_id)){
            $deviceArr = [];
            foreach ($soft_id as $k => $v){
                $deviceArr[] = self::getDevice($v);
            }
            return $deviceArr;
        }
    }
}
