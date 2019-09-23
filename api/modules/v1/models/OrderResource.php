<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "{{%order_resource}}".
 *
 * @property integer $order_id
 * @property string $resource
 */
class OrderResource extends \api\core\ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_resource}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'resource'], 'required'],
            [['order_id'], 'integer'],
            [['resource'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'resource' => 'Resource',
        ];
    }
}
