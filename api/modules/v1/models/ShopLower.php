<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "{{%shop_lower}}".
 *
 * @property integer $member_id
 * @property integer $shop_id
 */
class ShopLower extends \api\core\ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop_lower}}';
    }

    public function getShop(){
        return $this->hasOne(Shop::className(),['id'=>'shop_id'])->select('id');
    }

    public static function createLower($member_id,$shop_id)
    {
        $num = self::find()->where(['member_id'=>$member_id, 'shop_id'=>$shop_id])->count();
        if($num > 0){ return true; }
        $model = new self();
        $model->shop_id = $shop_id;
        $model->member_id = $member_id;
        return $model->save();
    }
}
