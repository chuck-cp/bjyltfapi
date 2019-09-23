<?php

namespace api\modules\v1\models;

use common\libs\ToolsClass;
use Yii;

/**
 * 订单支付信息表
 */
class LogPayment extends \api\core\ApiActiveRecord
{
    const PAY_TYPE_ALIPAY = 1;
    const PAY_TYPE_BANK = 2;
    const PAY_TYPE_WECHAT = 3;
    const PAY_TYPE_LINE = 4;

    public static function tableName()
    {
        return '{{%log_payment}}';
    }

    public function beforeSave($insert){
        if($insert){
            if($this->pay_type == self::PAY_TYPE_LINE){
                $this->payment_code = ToolsClass::randNumber(8);
            }
            $this->serial_number = $this->generateSerialNumber();
        }
        return parent::beforeSave($insert);
    }

    /*
     * 生成流水号
     * */
    public function generateSerialNumber(){
        return date('YmdHis').substr(microtime(),2,8).ToolsClass::randNumber(8);
    }
}
