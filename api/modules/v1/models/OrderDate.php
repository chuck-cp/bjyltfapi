<?php

namespace api\modules\v1\models;


use common\libs\RedisClass;
use common\libs\ToolsClass;
use Yii;
use yii\base\Exception;
use yii\data\Pagination;
use api\modules\v1\models\SystemAddress;
use yii\helpers\ArrayHelper;

/**
 * 店铺管理
 */
class OrderDate extends \api\core\ApiActiveRecord
{

    public static function tableName()
    {
        return '{{%order_date}}';
    }

    public function createOrderDate($order_id){
        try{
            $this->order_id = $order_id;
            $this->save();
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return 'ERROR';
        }
    }

    public function checkOrderStartTime(){
        if((strtotime(date('Y-m-d')) - strtotime($this->start_at) / 86400) < 10){
            return false;
        }
        if(strtotime($this->end_at) < strtotime($this->end_at)){
            return false;
        }
        return true;
    }

    /*
     * 获取当前投放时间的天数
     * */
    public function time_at($start_at="",$half_month=""){
        $month=ceil($half_month/2);
        $Odd=$half_month%2;
        $day= date('d',strtotime($start_at." 00:00:00"));
        $end_time=strtotime($this->start_at." 23:59:59 +{$month} month");
        if($day==1){
            if($Odd){
                $end_year=date("Y",$end_time);
                $end_month=date("m",$end_time)-1;
                return "{$end_year}-{$end_month}-15";
            }else{
                $end_time=$end_time-86400;
                $end_year=date("Y",$end_time);
                $end_month=date("m",$end_time);
                $end_day=date("d",$end_time);
                return "$end_year-$end_month-$end_day";

            }

        }else if($day==16){
            if($Odd){
                $end_year=date("Y",$end_time);
                $n_end_month=date("m",$end_time);
                $end_month=date("m",$end_time)-1;
                $time1="{$end_year}-{$n_end_month}-01 23:59:59";
                $end_day=date("d",strtotime($time1."- 1 day"));
                $ok_end_time="{$end_year}-{$end_month}-{$end_day}";
                return $ok_end_time;
            }else{
                $end_time=$end_time-86400;
                $end_year=date("Y",$end_time);
                $end_month=date("m",$end_time);
                $end_day=date("d",$end_time);
                return "$end_year-$end_month-$end_day";
            }
        }else{
            return false;
        }
    }
    /*
     * 天数转换为半月为单位的月数
     * */
    public function dayConvertMonth($total_day){
        return round($total_day / 15);
    }

    /*
     *修改广告投放时间
     * */
    public function checkDateTime($order_id){
        $order_list = self::find()->joinWith('order',$eagerLoading = false)->where(['yl_order_date.order_id'=>$order_id, 'member_id'=>Yii::$app->user->id])->select('member_id,yl_order.total_day,yl_order_date.start_at,yl_order_date.end_at,yl_order_date.is_update,yl_order.payment_status,yl_order.overdue_at, yl_order.advert_key, yl_order.number, yl_order.advert_time, yl_order.advert_id')->asArray()->one();
        //查询订单现在的状态
        $lock = Order::find()->where(['id'=>$order_id])->select('lock')->asArray()->one();
        if(isset($lock['lock']) && $lock['lock'] > 0){
            return ['ORDER_LOCKED',0];
        }

        //不允许修改
        if(!$order_list || $order_list['is_update'] > 2){
            return ['ORDER_NOT_ALLOWED_MODIFY',0];
        }

        //未付款
        if($order_list['payment_status']<1){
            return ['ORDER_UNPAID',0];
        }

        //判断天数是否一致
        $new_days = ToolsClass::timediff($this->end_at, $this->start_at);
        $old_days = ToolsClass::timediff($order_list['end_at'], $order_list['start_at']);
        if($new_days !== $old_days){
            return ['ORDER_TOTAL_DAYS_NOT_SAME',0];
        }

        //判断时间是否有修改,若没有修改返回
        if($order_list['start_at'] == $this->start_at || $order_list['end_at'] == $this->end_at){
            return ['ORDER_NO_MODIFY',0];
        }

        //判断修改后的日期是否在现在的时间+15天之后
        $update_days = ToolsClass::timediff(date('Y-m-d'),$this->start_at);
        if($update_days < 15){
            return ['ORDER_DATE_NOT_ALLOWED',0];
        }

        if(empty($this->start_at) || empty($this->end_at) || $this->end_at < $this->start_at){
            return ['ERROR',0];
        }
        $dbTrans = Yii::$app->db->beginTransaction();
        try{
            //向redis 里写入具体被修改的日期
            $orderAreaModel = OrderArea::find()->where(['order_id'=>$order_id])->one();
            //若原订单下没有街道可买
            if(!$orderAreaModel->street_area){
                $areaArr = ToolsClass::explode(',',$orderAreaModel->area_id);
                $len = strlen($areaArr[0]);
                if($len == 12){
                    $orderAreaModel->street_area = $orderAreaModel->area_id;
                }else {
                    $newArr = SystemAddress::find()->where(['in', 'left(id,5)', $areaArr])->andWhere(['is_buy' => 1, 'level' => 6])->select('id')->asArray()->all();
                    if (empty($newArr)) {
                        return ['ERROR', 0];
                    }
                    $orderAreaModel->street_area = implode(',', ArrayHelper::getColumn($newArr, 'id'));
                }
                $orderAreaModel->save();
            }
            if($this->start_at > $order_list['start_at'] && $this->start_at <= $order_list['end_at']){
                $add_begin = date('Y-m-d',strtotime($order_list['end_at'])+86400);
                $add_end = $this->end_at;
                $delete_date = $order_list['start_at'].','.date('Y-m-d',strtotime("-1 day ".$this->start_at));
            }elseif($this->end_at >= $order_list['start_at'] && $this->end_at < $order_list['end_at']){
                $add_begin = $this->start_at;
                $add_end = date('Y-m-d',strtotime("-1 day ".$order_list['start_at']));
                $delete_date = date('Y-m-d',strtotime("+1 day ".$this->end_at)).','.$order_list['end_at'];
            }elseif ($this->end_at < $order_list['start_at'] || $this->start_at > $order_list['end_at']){
                $add_begin = $this->start_at;
                $add_end = $this->end_at;
                $delete_date = $order_list['start_at'].','.$order_list['end_at'];
            }
            $postionInfo = AdvertPosition::findOne($order_list['advert_id']);
            $task_number = count(ToolsClass::generateDateList($add_begin,$add_end)) * SystemAddress::getStreetNumber(ToolsClass::explode(",",$orderAreaModel->street_area));
            RedisClass::rpush("system_create_order_list",json_encode([
                'order_id'=>$order_id,
                'advert_key'=>strtolower($order_list['advert_key']),
                'rate'=>$order_list['number'],
                'start_at'=>$add_begin,
                'end_at'=>$add_end,
                'delete_date'=>$delete_date,
                'area_id'=>$orderAreaModel->street_area,
                'advert_time'=>$order_list['advert_time'],
                'task_number'=>$task_number,
                'bind'=>$postionInfo->bind,
                'group'=>$postionInfo->group,
                'overdue_at'=>date('Y-m-d',strtotime('-7 day '.$this->start_at)),
                'type'=>'update_order',
                'token'=>md5("wwwbjyltfcom{$order_list['advert_time']}{$order_list['advert_key']}{$order_list['number']}{$order_list['member_id']}")
            ]),4);
            $dbTrans->commit();
            return ['SUCCESS',$order_list['is_update']];
        }catch(Exception $e){
            $dbTrans->rollBack();
            return ['ERROR',0];
        }
    }
    /*
     * 修改订单时间
     * */
    public function updateOrderDate($order_id,$is_update){
        $transaction = Yii::$app->db->beginTransaction();
        try {
            //用户消息
            OrderMessage::Log($order_id,"投放时间更改为".$this->start_at);
            self::updateAll(array('start_at'=>$this->start_at,'end_at'=>$this->end_at,'is_update'=>$is_update+1),'order_id='.$order_id);
            $transaction->commit();
            return true;
        }catch (\Exception $e){
            $transaction->rollBack();
            return false;
        }
    }

    public function getOrder()
    {
        //第一个参数为要关联的子表模型类名，
        //第二个参数指定 通过子表的customer_id，关联主表的id字段
        return $this->hasOne(Order::className(), ['id' => 'order_id'])->select('id');
    }

    /*
      * 场景
      * */
    public function scenes()
    {
        return [
            'create'=>[
                'start_at'=>[
                    'required'=>'1',
                    'result'=>'ORDER_START_AT_EMPTY'
                ],
                'end_at'=>[
                    'required'=>'1',
                    'result'=>'ORDER_END_AT_EMPTY'
                ],
            ],
            'orderdate'=>[
                'start_at'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'ORDER_START_AT_EMPTY'
                        ],
                        [
                            'function'=>'this::checkOrderStartTime',
                            'result'=>'ORDER_START_AT_ERROR'
                        ],
                    ]
                ],
                'end_at'=>[
                    'required'=>'1',
                    'result'=>'ORDER_START_AT_EMPTY'
                ],
            ],
        ];
    }
}
