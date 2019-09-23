<?php

namespace api\modules\v1\models;

use common\libs\DataClass;
use common\libs\RedisClass;
use Yii;
use yii\base\Exception;
use yii\db\Expression;

/**
 * 用户地区统计
 */
class MemberAreaCount extends \api\core\ApiActiveRecord
{

    public static function tableName()
    {
        return '{{%member_area_count}}';
    }

    /*
     * 获取我的地区
     * */
    public function getMemberAreaList(){
        $areaModel = self::find()->where(['member_id'=>Yii::$app->user->id])->select('area,shop_number,screen_number')->asArray()->all();
        if(empty($areaModel)){
            return [];
        }
        foreach($areaModel as $key=>$area){
            $areaModel[$key]['name'] = SystemAddress::getAreaNameById($area['area']);
        }
        return $areaModel;
    }

    /*
     * 根据地区获取屏幕数量
     * @params area string 地区ID(多个地区ID以逗号分割)
     * */
    public static function getScreenNumberByArea($area){
        if(strstr($area,',')){
            $area = explode(',',$area);
        }else{
            $area = [$area];
        }
        $screenNumberResult = RedisClass::getPipeline($area,3,'system_screen_number:');
        $screen_number = 0;
        foreach($screenNumberResult as $number){
            $screen_number += $number;
        }
        return $screen_number;
    }
    /*
     * 写入用户地区信息
     */
    public static function createMemberArea($member_id,$area,$screen_number=0,$shop_number=0){
        try{
            $area = substr($area,0,9);
            $obj = self::find()->where(['member_id'=>$member_id, 'area'=>$area])->one();
            if(!$obj){
                $memberAreaCountModel = new MemberAreaCount();
                $memberAreaCountModel->member_id = $member_id;
                $memberAreaCountModel->area = $area;
                $memberAreaCountModel->screen_number = $screen_number;
                $memberAreaCountModel->shop_number = $shop_number;
                $memberAreaCountModel->save();
            }
            self::updateAll(['screen_number'=>new Expression('screen_number + '.$screen_number),'shop_number'=>new Expression('shop_number +'.$shop_number)],['member_id'=>$member_id, 'area'=>$area]);
            return true;
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return false;
        }

    }

}
