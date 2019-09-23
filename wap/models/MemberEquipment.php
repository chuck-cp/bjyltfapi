<?php

namespace wap\models;

use Yii;

/**
 * 用户设备
 */
class MemberEquipment extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%member_equipment}}';
    }
    
}
