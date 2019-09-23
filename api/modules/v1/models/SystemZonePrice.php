<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "{{%system_zone_price}}".
 *
 * @property integer $area_id
 * @property string $price_id
 */
class SystemZonePrice extends \api\core\ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%system_zone_price}}';
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
            ],
        ];
    }
}
