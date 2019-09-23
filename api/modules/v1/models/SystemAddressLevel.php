<?php

namespace api\modules\v1\models;

use Yii;


/**
 * This is the model class for table "{{%system_address_level}}".
 *
 * @property string $area_id
 * @property integer $level
 * @property integer $type
 */
class SystemAddressLevel extends \api\core\ApiActiveRecord
{
    public $screen_number;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%system_address_level}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['area_id'], 'required'],
            [['area_id', 'level', 'type'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'area_id' => 'Area ID',
            'level' => 'Level',
            'type' => 'Type',
        ];
    }
    //根据地区id获取等级
    public static function getLevel($area_id)
    {
        if(!$obj = self::find()->where(['area_id'=>substr($area_id,0,9),'type'=>1])->one()){
            return 3;
        }
        if ($obj->level > 0) {
            return $obj->level;
        }
        return 3;
    }
    /*
    * 场景
    * */
    public function scenes(){
        return [
            'brokerage'=>[
                'area_id'=>[
                    'required'=>'1',
                    'result'=>'AREA_ID_EMPTY'
                ],
                'screen_number' => [
                    'required'=>'1',
                    'result'=>'SCREEN_NUMBER_EMPTY'
                ],
            ],
        ];
    }
}
