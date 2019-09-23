<?php

namespace pc\models;
use Yii;
class LogExamine extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%log_examine}}';
    }

    public function getDataByFid($foreign_id){

        return LogExamine::find()->select('id,foreign_id,examine_result,examine_desc,status')->where(['foreign_id'=>$foreign_id,'examine_key'=>5,'examine_result'=>2,'status'=>0])->asArray()->one();
    }

    public function updateSs($id){
        $model = self::find()->select('id,foreign_id,examine_result,examine_desc,status')->where(['id'=>$id])->one();
        $model->status=1;
        return $model->save();

    }

}