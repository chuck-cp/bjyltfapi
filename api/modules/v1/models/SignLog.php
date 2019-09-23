<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "{{%sign_log}}".
 *
 * @property string $id
 * @property string $member_id
 * @property string $member_name
 * @property string $team_id
 * @property string $team_name
 * @property integer $team_type
 * @property string $content
 * @property string $create_at
 */
class SignLog extends ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sign_log}}';
    }

    public static function writeLog($team_id,$team_name,$team_type,$content){
        $logModel = new self();
        $logModel->member_id = \Yii::$app->user->id;
        $logModel->member_type = SignTeamMember::getMemberType(Yii::$app->user->id);
        $logModel->member_name = Member::findOne(Yii::$app->user->id)->getAttribute('name');
        $logModel->team_id = $team_id;
        $logModel->team_name = $team_name;
        $logModel->team_type = $team_type;
        $logModel->content = $content;
        try{
            $logModel->save();
            return true;
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return false;
        }
    }

}
