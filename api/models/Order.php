<?php

namespace api\models;

use api\core\ApiActiveRecord;
use common\libs\ArrayClass;
use common\libs\RedisClass;
use common\libs\ToolsClass;
use Yii;
use yii\base\Exception;
use yii\data\Pagination;
use yii\db\Expression;

/**
 * 店铺管理
 */
class Order extends ApiActiveRecord
{

    public static function tableName()
    {
        return '{{%order}}';
    }

    /*
     * 支付回调
     * @param string serial_number 支付流水号
     * @param string payment_price 支付金额
     * @param string payment_at 支付时间
     * @param string other_account 第三方平台账号
     * @param string other_serial 第三方流水号
     * */
    public function paymentCallBack($serial_number,$payment_price,$payment_at,$other_account,$other_serial){
        $paymentModel = LogPayment::find()->where(['serial_number'=>$serial_number])->select('order_id,id,pay_status,price')->orderBy('id desc')->limit(1)->asArray()->one();
        if(empty($paymentModel)){
            \Yii::error('[error]找不到支付流水信息,流水ID:'.$paymentModel['id'],'payment');
            return false;
        }
        if($paymentModel['pay_status'] == 1){
            \Yii::error('[error]该订单已支付,流水ID:'.$paymentModel['id'],'payment');
            return false;
        }
//        if(($payment_price*100) != $paymentModel['price']){
//            \Yii::error('[error]支付金额不正确,流水ID:'.$paymentModel['id'],'payment');
//            return false;
//        }
        $order_id = $paymentModel['order_id'];
        $orderModel = Order::find()->where(['id'=>$order_id])->select('salesman_id,member_id,advert_id,total_day,advert_key,number,advert_time,payment_type,payment_status,examine_status')->asArray()->one();
        if(empty($orderModel)){
            \Yii::error('[error]找不到订单信息,流水ID:'.$paymentModel['id'].',订单号:'.$order_id,'payment');
            return false;
        }
        $dbTrans = \Yii::$app->db->beginTransaction();
        try{
            $payment_price = ToolsClass::priceConvert($payment_price,2);
            LogPayment::updateAll(['other_account'=>$other_account,'other_serial'=>$other_serial,'pay_at'=>$payment_at,'pay_status'=>1],['id'=>$paymentModel['id']]);
            $flag = false;
            if($orderModel['payment_type'] == 1){
                $resultLog = OrderMessage::Log($order_id,"完成付款");
                if($resultLog){
                    $resultLog = OrderMessage::Log($order_id,"广告素材待提交",2);
                }
                //全款支付
                Order::updateAll(['last_payment_at'=>$payment_at,'payment_at'=>$payment_at,'payment_status'=>3],['id'=>$order_id]);
                $flag = true;
            }else{
                //定金支付
                if($orderModel['payment_status'] == 0){
                    $resultLog = OrderMessage::Log($order_id,"完成首付款");
                    if($resultLog){
                        $resultLog = OrderMessage::Log($order_id,"广告素材待提交",2);
                    }
                    //第一次支付
                    Order::updateAll(['last_payment_at'=>$payment_at,'payment_at'=>$payment_at,'payment_status'=>1],['id'=>$order_id]);
                    $flag = true;
                }elseif($orderModel['payment_status'] == 1 || $orderModel['payment_status'] == 2){
                    $resultLog = OrderMessage::Log($order_id,"完成尾款");
                    //第二次支付
                    Order::updateAll(['last_payment_at'=>$payment_at,'payment_status'=>3],['id'=>$order_id]);
                }else{
                    throw new Exception('[error]订单状态错误,流水ID:'.$paymentModel['id'].',订单号:'.$order_id);
                }
            }
//            if(empty($resultLog)){
//                throw new Exception("[error]订单日志写入失败");
//            }
            if(!SystemAccount::UpdateAccount($paymentModel['price'])){
                throw new Exception("[error]系统总收入计算失败");
            }
            //付款成功增加业务员订单数量业绩统计
            /*************************************/
            if($flag == true){
                MemberAccount::updateAllCounters(['order_number'=>1], ['member_id'=>$orderModel['salesman_id']]);
                $date = date('Y-m',strtotime($payment_at));
                $accountAccountModel = MemberAccountCount::getOrCreateAccount($orderModel['salesman_id'],$date);
                if(!$accountAccountModel){
                    throw new Exception("[error]按月统计订单业绩失败");
                }
                if(!MemberAccountCount::updateAll(['order_number'=>new Expression('order_number + 1')],['create_at'=>$date,'member_id'=>$orderModel['salesman_id']])){
                    throw new Exception("[error]按月统计订单业绩失败");
                }
            }
            /*************************************/
            //首次付款成功,开始写入排期
            if($orderModel['payment_status'] == 0){
                $orderDateModel = OrderDate::find()->where(['order_id'=>$order_id])->select('start_at,end_at')->one();
                $orderAreaModel = OrderArea::find()->where(['order_id'=>$order_id])->select('area_id')->one();
                $advertModel = AdvertPosition::find()->where(['id'=>$orderModel['advert_id']])->select('`group`,bind')->one();
                RedisClass::rpush("system_create_order_list",json_encode([
                    'type'=>'create_order',
                    'order_id'=>$order_id,
                    'delete_date'=>'',
                    'advert_key'=>strtolower($orderModel['advert_key']),
                    'rate'=>$orderModel['number'],
                    'start_at'=>$orderDateModel['start_at'],
                    'end_at'=>$orderDateModel['end_at'],
                    'area_id'=>$orderAreaModel['area_id'],
                    'group'=>$advertModel['group'],
                    'bind'=>strtolower($advertModel['bind']),
                    'advert_time'=>$orderModel['advert_time'],
                    'token'=>md5("wwwbjyltfcom{$orderModel['advert_time']}{$orderModel['advert_key']}{$orderModel['number']}{$orderModel['member_id']}")
                ]),4);
            }
            $dbTrans->commit();
            return true;
        }catch (Exception $e){
            $dbTrans->rollBack();
            \Yii::error($e->getMessage(),'payment');
            return false;
        }
    }


}
