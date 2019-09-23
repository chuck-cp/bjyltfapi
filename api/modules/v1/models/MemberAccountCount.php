<?php

namespace api\modules\v1\models;

use common\libs\ToolsClass;
use Yii;

/**
 * 业绩按月统计
 */
class MemberAccountCount extends \api\core\ApiActiveRecord
{
    const INCOME = 1;
    const PAY = 2;
    const PERSONAL = 1;

    public static function tableName()
    {
        return '{{%member_account_count}}';
    }

    public function getMemberAccount(){
        $data = self::find()->where(['member_id'=>Yii::$app->user->id,'create_at'=>$this->create_at])->select('install_screen_number,shop_number,order_number')->asArray()->one();
        if(!empty($data)){
            $data['screen_number'] = $data['install_screen_number'];
        }
        return $data;
    }

    public function getMemberAccountDayList(){
        return self::find()->where(['member_id'=>Yii::$app->user->id])->select('create_at')->orderBy('id desc')->asArray()->all();
    }

    //写入按月统计的金钱信息
    public function loadAccountCount($type,$price,$screen_number=0,$shop_number = 0){
        if($type == self::INCOME){
            $this->count_price += $price;
            if($screen_number > 0){
                $this->screen_number += $screen_number;
                $this->shop_number += $shop_number;
            }
        }
    }

    public static function getOrCreateAccount($member_id,$create_at=''){
        $accountModel = new MemberAccountCount();
        $create_at = empty($create_at) ? date('Y-m') : $create_at;
        if($resultModel = $accountModel->findOne(['member_id'=>$member_id,'create_at'=>$create_at])){
            return $resultModel;
        }
        $accountModel->member_id = $member_id;
        $accountModel->create_at = $create_at;
        if($accountModel->save()){
            return $accountModel;
        }
        return false;
    }
    /*
     * 场景
     * */
    public function scenes(){
        return [
            'view'=>[
                'create_at'=>[]
            ],
        ];
    }
}
