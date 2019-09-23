<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "{{%order_copyright}}".
 *
 * @property integer $id
 * @property integer $order_id
 * @property string $image_url
 */
class OrderCopyright extends \api\core\ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_copyright}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'image_url'], 'required'],
            [['order_id'], 'integer'],
            [['image_url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'image_url' => 'Image Url',
        ];
    }
}
