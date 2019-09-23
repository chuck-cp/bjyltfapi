<?php

namespace api\modules\v1\models;

use Yii;


class LogOperation extends \api\core\ApiActiveRecord
{

    public static function tableName()
    {
        return '{{%log_operation}}';
    }

    public static function writeLog($foreign_id,$operation_type,$content) {
        try {
            $model = new LogOperation();
            $model->operation_type = $operation_type;
            $model->foreign_id = $foreign_id;
            $model->content = $content;
            $model->save();
            return true;
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            return false;
        }
    }
}
