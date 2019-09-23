<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use common\libs\ToolsClass;
use pms\modules\count\count;
use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "{{%sign_image}}".
 *
 * @property string $sign_id
 * @property integer $sign_type
 * @property string $image_url
 */
class SignImage extends ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sign_image}}';
    }
    //处理图片域名
    public function beforeSave($insert){
        if(parent::beforeSave($insert)){
            if($this->isNewRecord){
                $this->image_url = ToolsClass::replaceCosUrl($this->image_url);
            }
            return true;
        }
        return true;
    }
    public function saveImage(){
        try{
            $team_id = SignTeamMember::find()->where(['member_id'=>Yii::$app->user->id])->one()->getAttribute('team_id');
            $teamObj = SignTeam::findOne($team_id);
            $team_type = $teamObj ? $teamObj->team_type : '';
            if(!$team_type){
                return false;
            }
            $this->sign_type = $team_type;
            $this->save();
            return true;
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return false;
        }
    }

    /**
     * @return bool
     */
    public function checkThisImgUrl(){
        $arr = explode(',', $this->image_url);
        if (count($arr) != count(array_filter($arr))) {
            return false;
        }
        return true;
    }
    public function scenes(){
        return [
            'create' => [
//                'image_url' => [
//                    'required'=>'1',
//                    'result'=>'SIGN_IMAGE_URL_EMPTY'
//                ],
                'image_url' => [
                    [
                        [
                            'required' => '1',
                            'result' => 'SIGN_IMAGE_URL_EMPTY'
                        ],
                        [
                            'function' => 'this::checkThisImgUrl',
                            'result' => 'SIGN_IMAGE_URL_EMPTY'
                        ],
                    ]
                ],
            ],
        ];
    }

}
