<?php

namespace api\modules\v1\models;

use Yii;

/**
 * 发货物流状态 微信端使用
 */
class ShopLogistics extends \api\core\ApiActiveRecord
{

    public static function tableName()
    {
        return '{{%shop_logistics}}';
    }
}
