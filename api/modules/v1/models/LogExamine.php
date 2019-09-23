<?php

namespace api\modules\v1\models;

use Yii;
use yii\base\Exception;

/**
 * 审核日志
 */
class LogExamine extends \api\core\ApiActiveRecord
{
    const EXAMINE_KEY_1 = 1;   // 理发店设备申请审核
    const EXAMINE_KEY_2 = 2;   // 实名认证审核
    const EXAMINE_KEY_3 = 3;   // 业绩提现-财务审核
    const EXAMINE_KEY_4 = 4;   // 理发店设备安装审核
    const EXAMINE_KEY_5 = 5;   // 广告素材审核
    const EXAMINE_KEY_6 = 6;   // 电工认证审核
    const EXAMINE_KEY_7 = 7;   // 理发店总部信息审核
    const EXAMINE_KEY_8 = 8;   // 理发店店铺信息修改审核
    const EXAMINE_KEY_9 = 9;   // 系统等待日广告审核
    const EXAMINE_KEY_10 = 10;   // 写字楼LED设备申请审核
    const EXAMINE_KEY_11 = 11;   // 写字楼画报申请审核
    const EXAMINE_KEY_12 = 12;   // 公园画报申请审核
    const EXAMINE_KEY_13 = 13;   // 写字楼LED设备安装审核
    const EXAMINE_KEY_14 = 14;   // 写字楼画报安装审核
    const EXAMINE_KEY_15 = 15;   // 公园画报安装审核

    public static function tableName()
    {
        return '{{%log_examine}}';
    }

    /*
     * 获取店铺审核日志
     * */
    public static function getExamineByShop($shop_id,$examine_key=1){
        if($examineModel = self::find()->where(['foreign_id'=>$shop_id,'examine_key'=>$examine_key])->select('examine_desc,create_at')->orderBy('id desc')->limit(1)->asArray()->one()){
            return [
                'fail_reason'=>$examineModel['examine_desc'],
                'auditing_time'=>$examineModel['create_at'],
                'auditing_user'=> NULL
            ];
        }
        return [
            'fail_reason'=> '',
            'auditing_time'=> '',
            'auditing_user'=> NULL
        ];
    }

    /*
     * 写日志
     * */
    public static function writeLog($examine_key,$foreign_id){
        try{
            $logModel = new LogExamine();
            $logModel->examine_key = $examine_key;
            $logModel->foreign_id = $foreign_id;
            $logModel->create_user_id = 0;
            $logModel->create_user_name = '系统';
            $logModel->save();
            return true;
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return false;
        }

    }

    /*
     * 获取审核结果
     * */
    public static function getExamineResult($foreign_id,$examine_key,$result_field = 'examine_desc'){
        $logModel = LogExamine::find()->where(['foreign_id'=>$foreign_id,'examine_key'=>$examine_key])->orderBy('id desc')->limit(1)->select('examine_result,examine_desc')->asArray()->one();
        if(!empty($logModel)){
            if ($result_field) {
                return $logModel[$result_field];
            }
            return $logModel;
        }
    }
}
