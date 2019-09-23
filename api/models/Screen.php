<?php

namespace api\models;

use api\core\ApiActiveRecord;

/**
 * 屏幕管理
 */
class Screen extends ApiActiveRecord
{

    public static function tableName()
    {
        return '{{%screen}}';
    }

    public function getShop() {
        return $this->hasOne(Shop::className(),['id' => 'shop_id']);
    }
}
