<?php

namespace api\modules\v1\models;

use Yii;

/**
 * 店铺图片
 */
class ShopImage extends \api\core\ApiActiveRecord
{

    public static function tableName()
    {
        return '{{%shop_image}}';
    }

    /*
     * 获取店铺图片
     * */
    public function getShopImages($shop_id){
        return self::find()->where(['shop_id'=>$shop_id])->select('image_url')->asArray()->all();
    }
}
