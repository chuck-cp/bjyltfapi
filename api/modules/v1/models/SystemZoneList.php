<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "{{%system_zone_list}}".
 *
 * @property integer $id
 * @property string $price
 * @property integer $create_user_id
 * @property string $update_at
 */
class SystemZoneList extends \api\core\ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%system_zone_list}}';
    }

}
