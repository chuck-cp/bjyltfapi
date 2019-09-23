<?php
namespace pc\controllers;
use pc\core\PcController;
use pc\models\Order;
use pc\models\AdvertPosition;
use pc\models\OrderDate;
use pc\models\OrderPlayPresentation;
use pc\models\OrderPlayPresentationList;
use Yii;
use yii\data\Pagination;



/**
 * 投放历史
 */
class HistoryController extends PcController
{
	public $order;
	public $request;
	public function init(){
        parent::init();
        $this->order = new Order();
        $this->request = Yii::$app->request;
    }
	//投放历史记录
    public function actionDelivery()
    {
        if(!$uid = \Yii::$app->user->id) {
            return $this->redirect(['/index/index']);
        }
    	$orderlistmodel = $this->order->orderlist();
    	$pagination = new Pagination(['totalCount'=>$orderlistmodel->count(),'pageSize' =>20]);
       	$orderlistmodel = $orderlistmodel->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $advertname = new AdvertPosition;
        $name =$advertname->advertname();
        //var_dump($name);die;
    	return $this->render('delivery',['orderlist'=>$orderlistmodel,'pagination'=>$pagination,'name'=>$name,'advert_id'=>0,'sort'=>1,'order_id'=>'']);
    }
    //投放记录搜索
    public function actionSearch()
    {
    	$data['order_id'] = $this->request->get('order_id','');
    	$data['sort'] = $this->request->get('sort','');
    	$data['advert_id'] = $this->request->get('advert_id','');
        if($data['sort']=="asc"){
            $name['sort'] = "投放日期升序排列";
        }elseif($data['sort']=="desc"){
            $name['sort'] = "投放日期降序排列";
        }else{
            $name['sort'] = "全部";
        }
    	$orderlistmodel = $this->order->orderlist($data);
        // 使用总数来创建一个分页对象
        $pagination = new Pagination(['totalCount' => $orderlistmodel->count(),'pageSize'=>20]);
        $orderlistmodel = $orderlistmodel->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $advertname = new AdvertPosition;
        $aname =$advertname->advertname();
        //var_dump($page);die;
        return $this->render('delivery',['orderlist'=>$orderlistmodel,'pagination'=>$pagination,'name'=>$aname,'advert_id'=>$data['advert_id'],'sort'=>$data['sort'],'order_id'=>$data['order_id']]);
    }
    //订单详情
    public function actionOrderone()
    {
        if(!$uid = \Yii::$app->user->id) {
            return $this->redirect(['/index/index']);
        }
        $data['order_id']=$this->request->get('order_id',''); 
    	$orderinfo = $this->order->orderone($data);
        //var_dump($orderinfo);die;
    	$hou_zui = substr($orderinfo['info']['resource'],strripos($orderinfo['info']['resource'],"."));
    	$type = $this->is_image($hou_zui);
    	if($type=="v"){
    		$orderinfo['vcode'] = $this->format($hou_zui);
    	}
        //var_dump($orderinfo['vcode']);die;
        $orderlist = $this->order->orderlist('',1,$orderinfo['info']['type'],$orderinfo['info']['advert_time'])->asArray()->all();
        //$orderlistmodel=$orderlist->createCommand()->getRawSql();
        //var_dump($orderlistmodel);die;
    	return $this->render( 'orderone',['orderinfo'=>$orderinfo,'orderlist'=>$orderlist]);
    }
    //将历史订单信息导入新的订单里
    public function actionOrdernew()
    {	
        $data['order_code']=$this->request->get('order_code','');
        $data['order_id']=$this->request->get('info','');
    	$info=$this->order->orderone($data);
        //var_dump($info);die;
        $data['resource'] = $info['info']['resource'];
        $data['resource_name'] = $info['info']['resource_name'];
        $data['resource_thumbnail'] = $info['info']['resource_thumbnail'];
        $data['video_id'] = $info['info']['video_id'];
        $data['type'] =  $info['info']['type'];
        $data['copy'] = $info['copy'];
        //var_dump($data);die;
    	$code = $this->order->ordercopy($data);
        //\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if($code==1){
            return '成功';
        }else{
            return '失败';
        }
        
    }
    //判断视频的格式
    public function format($Suffix)
    {
    	switch ($Suffix) {
    		case '.mp4':
    				$code = ".mp4";
    			break;
    		default:
    				$code = ".mp4";
    			break;
    	}
    	return $code;
    }
    //判断资源是图片还是视频
    public function is_image($suffix)
    {
        //var_dump(strtolower($suffix));die;
    	$img = array('.jpg','.jpeg','.png','.gif');
        $type="v";
    	if(in_array(strtolower($suffix),$img)){
    		$type = "m"; 
    	}
    	return $type;
    }
    //已播放完成报告
    public function actionReportlist(){
//        if(!$uid = \Yii::$app->user->id) {
//            return $this->redirect(['/index/index']);
//        }
            $request = \Yii::$app->request;
            $order_id= $request->get('orderid',0);
            $datelist = OrderDate::getOrderDateSeries($order_id);//获取连续的时间

            //街道分页
            $newdate = OrderPlayPresentationList::find()->where(['order_id'=>$order_id])->asArray()->all();
            if(!empty($newdate)){
                $pageSize =2;
                $pages = new Pagination(['totalCount' => count($newdate),'pageSize' => $pageSize]);
                $srr = array_slice($newdate,$pages->offset,$pages->limit);
            }
            $newtotal = OrderPlayPresentation::find()->where(['order_id'=>$order_id])->asArray()->one();
            return $this->renderPartial('reportlist', [
                'pages' => $pages,
                'srr' => $srr,
                'datelist' => $datelist,
                'newtotal' => $newtotal,
            ]);
    }


   	
}
