<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%system_country_code}}".
 *
 * @property string $id
 * @property string $country_code
 * @property integer $is_show
 */
class SystemCountryCode extends ApiActiveRecord
{
    const SHOW_STATUS = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%system_country_code}}';
    }

    public static function getAvailableCode(){
        return self::find()->where(['is_show'=>self::SHOW_STATUS])->select('id,country_code,country_name')->asArray()->all();
    }
}
