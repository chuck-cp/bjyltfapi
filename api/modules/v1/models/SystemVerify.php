<?php

namespace api\modules\v1\models;

use api\core\ApiModel;
use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "{{%system_account}}".
 *
 * @property integer $total
 * @property integer $adv_expend
 * @property integer $margin
 */
class SystemVerify extends ApiModel
{
    public static function afterSendVerify($mobile,$type){
        if($type == 4){
            //安装人员获取验证码
            //验证安装人员手机号和姓名是否正确
            $name = Yii::$app->request->get('name');
            if(empty($name)){
                return 'MEMBER_NAME_EMPTY';
            }
            $memberModel = Member::find()->where(['mobile'=>$mobile,'name'=>$name,'status'=>1])->select('id')->asArray()->one();
            if(empty($memberModel)){
                return 'MEMBER_CERT_NOT_EXAMINE';
            }
            if(!MemberInfo::find()->where(['member_id'=>$memberModel['id'],'electrician_examine_status'=>1])->count()){
                return 'MEMBER_CERT_NOT_EXAMINE';
            }
        }
        return 'SUCCESS';
    }
}
