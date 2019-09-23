<?php

namespace wap\models;

use Yii;

/**
 * This is the model class for table "{{%system_train}}".
 *
 * @property string $id
 * @property string $name
 * @property integer $type
 * @property integer $status
 * @property integer $sort
 * @property string $content
 * @property string $thumbnail
 * @property string $create_at
 * @property string $create_user_name
 * @property string $create_user_id
 */
class SystemTrain extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%system_train}}';
    }
    /*
     * 获取培新资料图文内容
     */
    public function getTrainContent($id, $field){
        if(!$id || !$field) {return false;}
        $content = self::find()->where(['id'=>$id])->select($field)->asArray()->one();
        return isset($content[$field]) ? $content[$field] : '暂无内容';
    }

}
