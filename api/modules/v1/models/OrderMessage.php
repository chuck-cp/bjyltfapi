<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "{{%order_message}}".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $type
 * @property string $desc
 * @property string $create_at
 */
class OrderMessage extends \api\core\ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_message}}';
    }

    public static function Log($order_id,$desc,$type=1){
        $model = new OrderMessage();
        $model->order_id = $order_id;
        $model->desc = $desc;
        $model->type = $type;
        return $model->save();
    }
}
