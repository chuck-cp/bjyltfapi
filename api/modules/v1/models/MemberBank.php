<?php

namespace api\modules\v1\models;

use common\libs\DataClass;
use Yii;

/**
 * 银行卡
 */
class MemberBank extends \api\core\ApiActiveRecord
{
    public $verify;
    public static function tableName()
    {
        return '{{%member_bank}}';
    }
    /*
     * 获取我的所有银行卡
     * */
    public function getMemberBank(){
        $memberBank = self::find()->where(['member_id'=>Yii::$app->user->id, 'status' => 1])->asArray()->all();
        if(empty($memberBank)){
            return [];
        }
        $systemBank = SystemBank::systemBanks();
        foreach($memberBank as $key=>$bank){
            if (!isset($systemBank[$bank['bank_id']])) {
                continue;
            }
            $memberBank[$key]['bank_logo'] = $systemBank[$bank['bank_id']]['logo'];
            $memberBank[$key]['bank_back'] = $systemBank[$bank['bank_id']]['back'];
            $memberBank[$key]['number'] = $this->reformBankNumber($bank['number']);
            if($bank['type'] == 2){
                $memberBank[$key]['bank_name'] = $systemBank[$bank['bank_id']]['name'];
            }
        }
        return $memberBank;
    }

    /*
     * 获取默认银行卡
     * */
    public function getDefaultBank(){
        $bankModel = MemberBank::find()->where(['member_id'=>Yii::$app->user->id, 'status' => 1])->select('id,number,bank_id')->limit(1)->asArray()->one();
        if(empty($bankModel)){
            return [];
        }
        $systemBank = SystemBank::systemBanks();
        $bankModel['bank_logo'] = $systemBank[$bankModel['bank_id']]['logo'];
        $bankModel['bank_name'] = $systemBank[$bankModel['bank_id']]['name'];
        $bankModel['number'] = $this->reformBankNumber($bankModel['number']);
        unset($bankModel['bank_id']);
        return $bankModel;
    }
    /*
     * 删除银行卡
     * */
    public function deleteMemberBank($bank_id){
        return self::deleteAll(['id'=>$bank_id,'member_id'=>Yii::$app->user->id]);
    }

    public function beforeSave($insert){
        if($insert){
            $this->member_id = Yii::$app->user->id;
        }
        return parent::beforeSave($insert);
    }
    /*
     * 绑定银行卡前,加载数据
     * */
    public function loadBank(){
        if($this->type == 2){
            return true;
        }
        $backList = SystemBank::systemBanks();
        if(isset($backList[$this->bank_id])){
            $bankModel = $backList[$this->bank_id];
            $this->bank_name = $bankModel['name'];
            return true;
        }
        return false;
    }

    /*
     * 将银行卡的前几位变成*号
     * */
    public function reformBankNumber($bank_number){
        $numberLen = strlen($bank_number) - 4;
        $resultNumber = '';
        for($i=0;$i<$numberLen;$i++){
            $resultNumber .= '*';
        }
        $resultNumber .= substr($bank_number,-4);
        return $resultNumber;
    }

    public function checkBandId(){
            if(!$this->bank_id){
                return false;
            }
            return true;
    }
    /*
     * 场景
     * */
    public function scenes(){
        return [
            'create'=>[
                'name' => [
                    'required'=>'1',
                    'result'=>'BANK_MEMBER_NAME_EMPTY'
                ],
//                'bank_id'=>[
//                    'required'=>'1',
//                    'result'=>'BANK_ID_EMPTY'
//                ],
                'bank_id' => [
                    'function' => 'this::checkBandId',
                    'result'=>'BANK_ID_EMPTY'
                ],
                'number'=>[
                    'required'=>'1',
                    'result'=>'BANK_NUMBER_EMPTY'
                ],
                'mobile'=>[
                    'required'=>'1',
                    'result'=>'BANK_MOBILE_EMPTY'
                ],
                'verify'=>[
                    'required'=>'1',
                    'result'=>'VERIFY_EMPTY'
                ],
                'bank_name' => [],
                //后加以区分账号类型
                'type' => [
                    'required'=>'1',
                    'result'=>'ACCOUNT_TYPE_EMPTY'
                ],
            ],
        ];
    }
}
