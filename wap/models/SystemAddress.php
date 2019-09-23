<?php

namespace wap\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;


/**
 * 系统地址
 */
class SystemAddress extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%system_address}}';
    }



    /*
    * 根据地区ID获取名称
    * */
    public static function getAreaNameById($area_id,$result='ALL'){
        $startLen = 5;
        //$countryId = substr($area_id,0,$startLen);
//        $redisKey = 'system_address_'.$countryId;
//        if(!$systemAddress = Yii::$app->cache->get($redisKey)){
//            if($systemAddress = self::find()->where(['or',['id'=>$countryId],['left(parent_id,5)'=>$countryId]])->select('id,name')->asArray()->all()){
//                $systemAddress = ArrayHelper::map($systemAddress,'id','name');
//                Yii::$app->cache->set($redisKey,json_encode($systemAddress));
//            }
//        }else{
//            $systemAddress = json_decode($systemAddress,true);
//        }
//        if(empty($systemAddress)){
//            return false;
//        }
        $resultArea = '';
        if($result == 'ALL'){
            $areaLen = strlen($area_id);
            while(True){
                if($startLen > $areaLen){
                    break;
                }
                $sAreaId = substr($area_id,0,$startLen);
                $resultArea .= SystemAddress::getName($sAreaId);
                if($startLen == 9){
                    $startLen += 3;
                }else{
                    $startLen += 2;
                }
            }
        }else{
            $resultArea .= SystemAddress::getName($area_id);
        }
        return $resultArea;
    }
    /*
 * 获取地区名称
 * */
    public static function getName($id){
        $addressModel = SystemAddress::find()->where(['id'=>$id])->select('name')->asArray()->one();
        if(!empty($addressModel)){
            return $addressModel['name'];
        }
    }




}