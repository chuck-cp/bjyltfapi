<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "{{%member_shop_date}}".
 *
 * @property string $id
 * @property integer $member_id
 * @property integer $type
 * @property string $date
 */
class MemberShopDate extends \api\core\ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_shop_date}}';
    }

    public static function createDate($member_id,$date,$type){
        if(MemberShopDate::find()->where(['member_id'=>$member_id,'type'=>$type,'date'=>$date])->count()){
            return true;
        }
        $model = new MemberShopDate();
        $model->member_id = $member_id;
        $model->date = $date;
        $model->type = $type;
        return $model->save();
    }

    public static function getShopDate($type){
        $dateCountList = MemberShopDate::find()->where(['member_id'=>Yii::$app->user->id,'type'=>$type])->select('date')->asArray()->all();
        if(empty($dateCountList)){
            return [];
        }
        $dateCountList = array_column($dateCountList,'date');
        foreach($dateCountList as $key=>$date){
            $reformDate[date('Y-m',strtotime($date))] = $key;
        }
        $reformDate = array_keys($reformDate);
        foreach($reformDate as $date){
            $dateMatch = explode("-",$date);
            $resultDate[$dateMatch[0]][] = $dateMatch[1];
        }
        foreach($resultDate as $key=>$date){
            $resultReform[] = [
                'years'=>(string)$key,
                'month'=>$date
            ];
        }
        return $resultReform;
    }
}
