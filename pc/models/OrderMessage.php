<?php

namespace pc\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * 订单状态改变消息
 */
class OrderMessage extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%order_message}}';
    }

    public function saveMessage($orderId){
        $this->order_id = $orderId;
        $this->type = 2;
        $this->desc = "广告资料进入待审核状态";
        return $this->save();
    }

}