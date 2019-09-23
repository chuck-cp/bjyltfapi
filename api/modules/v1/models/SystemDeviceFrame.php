<?php

namespace api\modules\v1\models;

use Yii;


class SystemDeviceFrame extends \api\core\ApiActiveRecord
{
    public static function tableName()
    {
        return '{{%system_device_frame}}';
    }

}
