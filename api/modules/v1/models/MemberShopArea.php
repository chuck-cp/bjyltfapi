<?php

namespace api\modules\v1\models;

use Yii;

/**
 * 用户店铺地区
 */
class MemberShopArea extends \api\core\ApiActiveRecord
{
    public $parent_id;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_shop_area}}';
    }

    public static function createArea($member_id,$area,$type){
        if(MemberShopArea::find()->where(['member_id'=>$member_id,'type'=>$type,'area'=>$area])->count()){
            return true;
        }
        $model = new MemberShopArea();
        $model->member_id = $member_id;
        $model->area = $area;
        $model->type = $type;
        return $model->save();
    }

    public function getArea(){
        $dateCountList = self::find()->where(['member_id'=>Yii::$app->user->id,'type'=>$this->type + 1])->select('area')->asArray()->all();
        if(empty($dateCountList)){
            return [];
        }
        $dateCountList = array_column($dateCountList,'area');
        return SystemAddress::getAreaByCache($dateCountList,$this->parent_id);
    }


    /*
     * 场景
     * */
    public function scenes(){
        return [
            'area'=>[
                'parent_id'=>[
                    'type'=>'int'
                ],
                'type'=>[
                    'type'=>'int'
                ],
            ],
        ];
    }
}
