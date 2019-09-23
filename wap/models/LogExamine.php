<?php

namespace wap\models;

use Yii;

class LogExamine extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%log_examine}}';
    }

}
