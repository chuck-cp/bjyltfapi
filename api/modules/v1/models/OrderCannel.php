<?php

namespace api\modules\v1\models;

use common\libs\RedisClass;
use common\libs\ToolsClass;
use api\modules\v1\models\OrderArea;
use Yii;

/**
 * This is the model class for table "{{%order_cannel}}".
 *
 * @property integer $order_id
 * @property string $cancel_cause
 * @property string $create_at
 */
class OrderCannel extends \api\core\ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_cannel}}';
    }

    /*
     * 放弃订单
     * */
    public function OrderCannel(){
        $model=new Order();
        $order_list = $model::find()->select('start_at,end_at,advert_id,payment_status,order_id,yl_order.id')->joinWith('date')->where(['yl_order.id'=>$this->order_id])->asArray()->one();
        $advertModel = AdvertPosition::find()->where(['id'=>$order_list['advert_id']])->select('bind,group')->asArray()->one();
        if($order_list['payment_status'] != 0){
            return false;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model->updateAll(array('payment_status' => -1), 'id=' . $this->order_id);
            $this->save();
            //向redis队列中写入取消订单的信息
            OrderMessage::Log($this->order_id, '放弃支付 原因：'.$this->cancel_cause);
            OrderMessage::Log($this->order_id,'订单关闭',2);
            $transaction->commit();
            return true;
        }catch (\Exception $e){
            Yii::error($e->getMessage(),'db');
            $transaction->rollBack();
            return false;
        }

    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id'], 'required'],
            [['order_id'], 'integer'],
            [['create_at'], 'safe'],
            [['cancel_cause'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'cancel_cause' => 'Cancel Cause',
            'create_at' => 'Create At',
        ];
    }
    public function scenes(){
        return [
            'ordercannel'=>[
                'order_id'=>[
                    'required'=>'1',
                    'order_id' => 'int',
                    'result'=>'ORDER_ID_EMPTY',
                ],
                'cancel_cause'=>[
                    'required'=>'1',
                    'result' => 'ORDER_CANCEL_CAUSE_EMPTY',
                ],
            ],
        ];
    }
}
