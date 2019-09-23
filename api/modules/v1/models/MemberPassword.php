<?php

namespace api\modules\v1\models;

use common\libs\ToolsClass;
use Yii;

/**
 * 支付密码
 */
class MemberPassword extends \api\core\ApiActiveRecord
{
    public $password;
    public $repeat_password;
    public $verify;
    public $mobile;

    public static function tableName()
    {
        return '{{%member_password}}';
    }

    public function updatePaymentPassword(){
        if(!$memberModel = self::findOne(['member_id'=>Yii::$app->user->id])){
            $memberModel = new self;
            $memberModel->member_id = Yii::$app->user->id;
        }
        $memberModel->payment_password = md5($this->password);
        return $memberModel->save();
    }

    /*
     * 验证是否设置支付密码
     * */
    public static function checkPaymentPassword($payment_password=''){
        $where['member_id'] = Yii::$app->user->id;
        if(!empty($payment_password)){
            $where['payment_password'] = md5($payment_password);
        }
        return MemberPassword::find()->where($where)->count();
    }
    /*
     * 场景
     * */
    public function scenes(){
        return [
            'payment-password'=>[
                'mobile'=>[
                    'required'=>'1',
                    'result'=>'MOBILE_EMPTY'
                ],
                'password'=>[
                    'required'=>'1',
                    'result'=>'PASSWORD_EMPTY'
                ],
                'repeat_password'=>[
                    'required'=>'1',
                    'result'=>'REPEAT_PASSWORD_EMPTY'
                ],
                'verify'=>[
                    'required'=>'1',
                    'result'=>'VERIFY_EMPTY'
                ]
            ]
        ];
    }
}
