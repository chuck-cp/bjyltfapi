<?php

namespace pc\models;
//use api\modules\v1\models\AdvertPosition;
use pc\models\OrderCopyright;
use pc\models\AdvertPosition;
use Yii;



/**
 * This is the model class for table "{{%order}}".
 *
 * @property string $id
 * @property string $member_id
 * @property string $member_name
 * @property string $salesman_name
 * @property string $salesman_mobile
 * @property string $order_code
 * @property string $order_price
 * @property integer $payment_type
 * @property string $payment_price
 * @property string $payment_at
 * @property string $screen_number
 * @property integer $rate
 * @property integer $advert_id
 * @property integer $advert_type
 * @property string $advert_time
 * @property string $create_at
 * @property integer $status
 * @property integer $examine_status
 */
class Order extends \yii\db\ActiveRecord
{
	public $order;
	public $advert_name;
	public $member_id;
	public function init(){
       parent::init();
       $this->member_id = \Yii::$app->user->id;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order}}';
    }
     public function getorderDate()
    {
        return $this->hasMany(OrderDate::className(), ['order_id' => 'id']);
    }
    public function getadvertPosition()
    {
        return $this->hasMany(AdvertPosition::className(), ['id' => 'advert_id']);
    }
 	//展示用户的所有订单列表
    public function orderlist($data='',$orone='',$type='',$advert_time='')
    {
    	if(!empty($this->member_id)){
    		$orderlistmodel = self::find()->joinWith('orderDate',$eagerLoading = false)->where(['yl_order.member_id'=>$this->member_id])->select('yl_order.id,yl_order.order_code,yl_order.advert_time,yl_order.resource,yl_order.resource_name,yl_order.resource_thumbnail,yl_order.advert_id,yl_order.examine_status,yl_order.advert_name,yl_order.video_id,yl_order.rate,yl_order_date.start_at,yl_order_date.end_at,yl_order.area_name,yl_order.screen_number');
            if(!empty($type)){
                $orderlistmodel = $orderlistmodel-> joinWith('advertPosition',$eagerLoading = false)->andWhere(['yl_advert_position.type'=>$type]);
                $time = date("Y-m-d H:i:s",strtotime("7 day",strtotime(date('Y-m-d H:i:s'))));
                $orderlistmodel = $orderlistmodel->andWhere('yl_order_date.start_at>=:time',[':time'=>$time]);
                 //$orderlistmodel=$orderlistmodel->createCommand()->getRawSql();
                 //var_dump($orderlistmodel);die;
            }
            if(empty($orone)){
                $orderlistmodel = $orderlistmodel->andWhere('yl_order.examine_status=5');
            }else{
                $orderlistmodel = $orderlistmodel->andWhere('yl_order.examine_status<3');
                $orderlistmodel = $orderlistmodel->andWhere(['yl_order.payment_status'=>[1,3]]);
            }
            if(!empty($advert_time)){
                $orderlistmodel = $orderlistmodel->andWhere(['yl_order.advert_time'=>$advert_time]);
            }
    		//var_dump($data);die;
    		if(!empty($data['order_id'])){
    			$this->order = $data['order_id'];
    		 	$orderlistmodel = $orderlistmodel->andWhere(['yl_order.order_code'=>$this->order]);
    		}
    		if(!empty($data['advert_id'])&&$data['advert_id'] !=='0'){
    			$this->advert_id = $data['advert_id'];
    			$orderlistmodel = $orderlistmodel->andWhere(['yl_order.advert_id'=>$this->advert_id]);
    		}
          //$orderlistmodel=$orderlistmodel->createCommand()->getRawSql();
    	//var_dump($orderlistmodel);die;
    		if(!empty($data['sort'])&&$data['sort']=='asc'){
    			$orderlistmodel = $orderlistmodel->orderBy(['yl_order_date.start_at'=>SORT_ASC]);
    		}elseif(!empty($data['sort'])&&$data['sort']=='desc'){
    			$orderlistmodel = $orderlistmodel->orderBy(['yl_order_date.start_at'=>SORT_DESC]);
    		}
    		return $orderlistmodel;
    	}
    }
    //订单详情
    public function orderone($data)
    {

    	$ordercopy = new OrderCopyright;
    	$orderinfomodel = $this->orderlist($data);
        $orderinfo['info'] = $orderinfomodel->asArray()->one();
        $AP = new AdvertPosition;
        $orderinfo['info']['type']=$AP->type($orderinfo['info']['advert_id']);
        //var_dump($orderinfo['info']);die;
    	$orderinfo['copy'] = $ordercopy->getimg($orderinfo['info']['id']);
    	return $orderinfo;
    }
    //修改订单的资源和添加知识产权
    public function ordercopy($data)
    {
    	$ordercopy = new OrderCopyright;
        //var_dump($data['order_code']);die;
        if(is_array($data['order_code'])){
            $orderModel = self::find()->joinWith('advertPosition',$eagerLoading = false)->where(['yl_order.order_code'=>$data['order_code'],'yl_advert_position.type'=>$data['type']])->all();
            //$orderlistmodel=$orderModel->createCommand()->getRawSql();
            //var_dump($orderModel);die;
            $transaction = self::getDb()->beginTransaction();
            try{
                foreach($orderModel as $k=>$v){
                    $v->resource = $data['resource'];
                    $v->resource_name = $data['resource_name'];
                    $v->resource_thumbnail = $data['resource_thumbnail'];
                    $v->video_id = $data['video_id'];
                    $v->examine_status = 1;
                    $v->save();
                    $copy['copy'] = $data['copy'];
                    foreach($copy['copy'] as $key=>$val){
                        $ordercopy->incopy($v->id,$val);
                    }
                    $orderMessage = new OrderMessage();
                    $orderMessage->saveMessage($v->id);    
                }  
                $transaction->commit();
                return 1;
            }catch (\Exception $e){
                $transaction->rollBack();
                return 0;
            }catch(\Throwable $e) {
                $transaction->rollBack();
                return 0;
            }
            
        }
    	
    }
	public function getAllByUid($where){
		$data = Order::find()->joinWith('date')->where($where)->asArray()->all();
		return $data;
	}
	public function getDate()
	{
		return $this->hasOne(OrderDate::className(), ['order_id' => 'id']);
	}
	public function getAdPosition()
	{
		return $this->hasOne(AdvertPosition::className(), ['id' => 'id']);
	}
//	public function getOrderDataByUid($uid,$status){
//		$data = Order::find()->where(['member_id'=>$uid,'examine_status'=>$status])->asArray()->all();
//		return $data;
//	}
    /*
        * 获取订单详情
         * */
    public function getOrderinfo($oerderid){
        $orderModel=self::find()->where(['id'=>$oerderid])->select('id,member_id,order_code,area_name,screen_number,advert_name,rate,resource,resource,resource_name,resource_thumbnail,advert_id,examine_status,advert_time,video_id,lock,resource_duration,resource_attribute,advert_key,deal_price,video_trans_url')->asArray()->one();
        if(empty($orderModel)){
            return [];
        }
        if ($orderModel['resource_attribute']) {
            $orderModel['resource_attribute'] = json_decode($orderModel['resource_attribute'],true);
            if (isset($orderModel['resource_attribute'][$orderModel['advert_key']])) {
                $orderModel['resource_attribute'] = $orderModel['resource_attribute'][$orderModel['advert_key']];
            } else {
                $orderModel['resource_attribute'] = [
                    'size' => '',
                    'sha1Sum' => '',
                    'name' => '',
                ];
            }
        } else {
            $orderModel['resource_attribute'] = [
                'size' => '',
                'sha1Sum' => '',
                'name' => '',
            ];
        }
        //获取投放日期
        $orderDate=OrderDate::find()->where(['order_id'=>$oerderid])->select('start_at,end_at')->asArray()->one();
        if(empty($orderDate)){
            $orderModel['start_at']='';
            $orderModel['end_at']='';
        }else{
            $orderModel['start_at']=$orderDate['start_at'];
            $orderModel['end_at']=$orderDate['end_at'];
        }
        //广告产权
        $orderCopyright=OrderCopyright::find()->where(['order_id'=>$oerderid])->select('id,image_url,name')->asArray()->all();
        if(empty($orderCopyright)){
            $orderModel['copyright']=array();
        }else{
            $orderModel['copyright']=$orderCopyright;
        }
        //获取广告类型
        $advertModel=AdvertPosition::find()->where(['id'=>$orderModel['advert_id']])->select('type')->asArray()->one();
        if(empty($advertModel)){
            $orderModel['type']=1;
        }else{
            $orderModel['type']=$advertModel['type'];
        }
        if($orderModel['examine_status']==2){
          $Logexamine=LogExamine::find()->where(['and',['foreign_id'=>$oerderid],['examine_key'=>5]])->select('examine_desc')->asArray()->one();
            if(empty($orderCopyright)){
                $orderModel['examine_desc']="";
            }else{
                $orderModel['examine_desc']=$Logexamine['examine_desc'];
            }
        }
        return $orderModel;
    }

    /*
    * 更新订单资源
     * type 0、图片 1、文本 2、音频
    * */
    public function updateOrder(){
        $advert_type = 0;
        if (in_array($this->advert_key,['A1','A2'])) {
            $advert_type = 2;
        } elseif (in_array($this->advert_key,['B','C'])){
            $advert_type = 0;
        }
        $data = Yii::$app->request->get();
        $this->resource = $data['resource'];
        $this->resource_name = $data['resource_name'];
        $this->video_id = $data['video_id'];
        $this->resource_thumbnail = $data['videoimg'];
        $this->examine_status = 1;
        $this->resource_duration = $data['duration'];
        $this->video_trans_url = isset($data['video_trans_url']) ? $data['video_trans_url'] : '';
        $this->resource_attribute = json_encode([
            $this->advert_key => [
                'type' => $advert_type,
                'size' => $data['videoSize'],
                /*'sha1Sum' => $data['videoSha1'],*/
                'name' => "ad_{$this->id}",
            ]
        ]);
        $orderMessage = new OrderMessage();
        $orderMessage->saveMessage($this->id);
        if($this->save()){
            return true;
        }else{
            return false;
        }
    }
}
















