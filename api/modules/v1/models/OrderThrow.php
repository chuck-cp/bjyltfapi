<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "{{%order_throw}}".
 *
 * @property integer $id
 * @property string $serial_number
 * @property string $area
 * @property string $start_at
 * @property string $end_at
 * @property integer $number
 * @property integer $status
 * @property string $resource
 * @property string $video_id
 */
class OrderThrow extends \api\core\ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_throw}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['serial_number', 'area', 'number', 'status'], 'integer'],
            [['start_at', 'end_at', 'resource'], 'required'],
            [['start_at', 'end_at'], 'safe'],
            [['resource'], 'string', 'max' => 255],
            [['video_id'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'serial_number' => 'Serial Number',
            'area' => 'Area',
            'start_at' => 'Start At',
            'end_at' => 'End At',
            'number' => 'Number',
            'status' => 'Status',
            'resource' => 'Resource',
            'video_id' => 'Video ID',
        ];
    }
}
