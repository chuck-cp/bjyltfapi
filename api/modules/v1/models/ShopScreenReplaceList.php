<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "{{%shop_screen_replace_list}}".
 *
 * @property string $id
 * @property string $replace_id
 * @property string $device_number
 * @property string $replace_device_number
 * @property string $replace_desc
 */
class ShopScreenReplaceList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop_screen_replace_list}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'replace_id'], 'integer'],
            [['device_number', 'replace_device_number'], 'string', 'max' => 30],
            [['replace_desc'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'replace_id' => 'Replace ID',
            'device_number' => 'Device Number',
            'replace_device_number' => 'Replace Device Number',
            'replace_desc' => 'Replace Desc',
        ];
    }

    public static function getSolft($id){
        $obj = self::findOne($id);
        if(!$obj){
            return false;
        }
        return SystemDevice::getSolf($obj->device_number);
   }
}
