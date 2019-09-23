<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "{{%advert_cell_out_area}}".
 *
 * @property string $id
 * @property string $advert_key
 * @property string $date
 * @property string $area_id
 * @property integer $rate
 * @property integer $advert_time
 */
class AdvertCellOutArea extends \api\core\ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%advert_cell_out_area}}';
    }

    public static function getDb(){
        return Yii::$app->get('throw_db');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['advert_key', 'date', 'area_id', 'rate', 'advert_time'], 'required'],
            [['date'], 'safe'],
            [['area_id', 'rate', 'advert_time'], 'integer'],
            [['advert_key'], 'string', 'max' => 5],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'advert_key' => 'Advert Key',
            'date' => 'Date',
            'area_id' => 'Area ID',
            'rate' => 'Rate',
            'advert_time' => 'Advert Time',
        ];
    }
}
