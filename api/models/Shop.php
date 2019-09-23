<?php

namespace api\models;

use api\core\ApiActiveRecord;

/**
 * 店铺管理
 */
class Shop extends ApiActiveRecord
{

    public static function tableName()
    {
        return '{{%shop}}';
    }
}
