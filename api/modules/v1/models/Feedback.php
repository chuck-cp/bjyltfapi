<?php

namespace api\modules\v1\models;

use Yii;


class Feedback extends \api\core\ApiActiveRecord
{

    public static function tableName()
    {
        return '{{%feedback}}';
    }

    public function beforeSave($insert){
        if($insert){
            $this->member_id = Yii::$app->user->id;
        }
        return parent::beforeSave($insert);
    }

    /*
     * 场景
     * */
    public function scenes(){
        return [
            'feedback'=>[
                'question'=>[],
                'content'=>[
                    'required'=>'1',
                    'result'=>'FEEDBACK_CONTENT_EMPTY'
                ]
            ],
        ];
    }
}
