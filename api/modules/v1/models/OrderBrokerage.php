<?php

namespace api\modules\v1\models;

use common\libs\ToolsClass;
use Yii;

/**
 * 订单佣金分配表
 */
class OrderBrokerage extends \api\core\ApiActiveRecord
{
    public static function tableName()
    {
        return '{{%order_brokerage}}';
    }


}

