<?php
namespace pc\controllers;
use common\libs\ToolsClass;
use pc\core\PcController;
use pc\models\Member;
use pc\models\MemberCopyright;
use pc\models\Order;
use pc\models\LogExamine;
use pc\models\OrderArea;
use pc\models\OrderCopyright;
use pc\models\OrderDate;
use pc\models\OrderThrowOrderDate;
use pc\models\SystemAddress;
use yii\data\Pagination;

/**
 * 首页
 */
class DeliveryController extends PcController
{

    public  $enableCsrfValidation=false;
    public $arr='';
    public function actionIndex(){
            if(!$uid = \Yii::$app->user->id) {
                return $this->redirect(['/index/index']);
            }
            $memberObj = new Member();
            $memberData = $memberObj->getOneData($uid);
            $orderData = $this->actionOrderData($uid);
            $logObj = new LogExamine();
            $message=[];
            foreach($orderData as $k=>$v){
                $data = $logObj->getDataByFid($v['id']);
                if(!empty($data)){
                    $a = $logObj->updateSs($data['id']);
                    $data['order_code'] = $v['order_code'];
                    array_push($message,$data);
                }
            }

            $jsonMsm=json_encode($message);
            return $this->render('index',[
               'memberData'=>$memberData,
               'orderData'=>$orderData,
                'message'=>$message,
                'jsonMsm'=>$jsonMsm
             ]);
    }
    /*
     * 投放素材详情
     * */
    public function actionInfo(){
        if(!$uid = \Yii::$app->user->id) {
            return $this->redirect(['/index/index']);
        }
        $request = \Yii::$app->request;
        $orderid = $request->get('orderid','');
        $orderModel=new Order();
        $orderinfo = $orderModel->getOrderinfo($orderid);
        if($orderinfo['advert_key']=="CD"){
            if(empty($orderinfo['resource'])){
                $orderinfo['resourceC']="";
                $orderinfo['resourceD']="";
            }else{
                $resource=explode(",",$orderinfo['resource']);
                $orderinfo['resourceC']=$resource['0'];
                $orderinfo['resourceD']=$resource['1'];
            }
            if(empty($orderinfo['resource_thumbnail'])){
                $orderinfo['resource_thumbnailC']="";
                $orderinfo['resource_thumbnailD']="";
            }else{
                $resource_thumbnail=explode(",",$orderinfo['resource_thumbnail']);
                $orderinfo['resource_thumbnailC']=$resource_thumbnail['0'];
                $orderinfo['resource_thumbnailD']=$resource_thumbnail['1'];
            }
            if(empty($orderinfo['resource_name'])){
                $orderinfo['resource_nameC']="";
                $orderinfo['resource_nameD']="";
            }else{
                $resource_thumbnail=explode(",",$orderinfo['resource_name']);
                $orderinfo['resource_nameC']=$resource_thumbnail['0'];
                $orderinfo['resource_nameD']=$resource_thumbnail['1'];
            }
            return $this->render('infocd',[
                'data'=>$orderinfo,
            ]);
        }elseif($orderinfo['advert_key']=="C" or $orderinfo['advert_key']=="D"){
            return $this->render('infoc',[
                'data'=>$orderinfo,
            ]);
        }else{
            $orderinfo['advert_time2']=ToolsClass::minuteCoverSecond($orderinfo['advert_time']);
            return $this->render('info',[
                'data'=>$orderinfo,
            ]);
        }

//

    }
    /*
     * 提交完成确认页面
      * */
    public function actionComplete(){
        if(!$uid = \Yii::$app->user->id) {
            return $this->redirect(['/index/index']);
        }
        return $this->render('complete');
    }

    /*
      * 获取所有产权
       * */
    public function actionCopyright(){
        if(!$uid = \Yii::$app->user->id) {
            return json_encode(array('status'=>200,'data'=>array()));
        }
        $uid = \Yii::$app->user->id;
       // $request = Yii::$app->request;
       // $uid = $request->get('order_id','');
        $copyrightModel=new MemberCopyright();
        $copyrightData=$copyrightModel->getall($uid);
        if(empty($copyrightData)){
            return json_encode(array('status'=>400,'data'=>array()));
        }else{
            return json_encode(array('status'=>200,'data'=>$copyrightData));
        }
    }
    /*
       * 更新订单资源
        * */
    public function actionResource(){
        $order_id = \Yii::$app->request->get('orderid');
        $orderModel = Order::findOne($order_id);
        $dbTrans = \Yii::$app->db->beginTransaction();
        try {
            if(!$orderModel->updateOrder()){
                $dbTrans->rollBack();
                return json_encode(array('status'=>400));
            }
            //更新订单版权
            $orderCopyrightModel = new OrderCopyright();
            $copyright = \Yii::$app->request->get('copyright');
            if(!$orderCopyrightModel->allincopy($order_id,$copyright)){
                $dbTrans->rollBack();
                return json_encode(array('status'=>401));
            }
            $dbTrans->commit();
            return json_encode(array('status'=>200));
        } catch (\Exception $e) {
            \Yii::error($e->getMessage());
            $dbTrans->rollBack();
            return json_encode(array('status'=>400));
        }
    }

    /*
     * 删除订单版权
     * */
    public function actionDeleteresource(){
        $request = \Yii::$app->request;
        $id=$request->get('resourceid','');
        if(OrderCopyright::deleteAll(['id'=>$id])){
            return json_encode(array('status'=>200));
        }else{
            return json_encode(array('status'=>400));
        }

    }
    /*
       * 视频转码播放
        * */
    public function actionTranscoding(){




        // 1252719796-transcode-30faa737a5624f2a8432ca0b8e82f640t0
        $s = \Yii::$app->cosB->createSignVideo();
        $a = \Yii::$app->cosB->curlApi('http://www.baidu.com','DescribeTaskDetail',$s,"1252719796-transcode-30faa737a5624f2a8432ca0b8e82f640t0");
        print_r($a);
        exit;
        $request = \Yii::$app->request;
        $fileid= $request->get('fileid','');

        $setImageUrl=\Yii::$app->cosB->createSignByGetVideo("vod.api.qcloud.com/v2/index.php",
            [
            'Action'=>'ConvertVodFile',
            'fileId'=>$fileid,
            'isScreenshot'=>0,
            'isWatermark'=>0,
        ]);
        \Yii::error('['.date('Y-m-d H:i:s').'素材上传成功1]'.$setImageUrl);
        $resultSet =ToolsClass::curl('https://'.$setImageUrl,'','GET');
        \Yii::error('['.date('Y-m-d H:i:s').'素材上传成功2]'.$resultSet);
        $resultSet = json_decode($resultSet,true);
        print_r($resultSet);exit;
        if($resultSet['code'] == 0){
            $callbackUrl = \Yii::$app->cosB->createSignByGetVideo("vod.tencentcloudapi.com",
                [
                    'Action'=>'DescribeTaskDetail',
                    'TaskId'=>$resultSet['vodTaskId'],
                ]);
            return json_encode(array('status'=>200,'url' => $callbackUrl));
        }else{
            return json_encode(array('status'=>400));
        }
    }


    public function actionThrowarea(){
        if(!$uid = \Yii::$app->user->id) {
            return $this->redirect(['/index/index']);
        }
        $request = \Yii::$app->request;
        $order_id= $request->get('orderid',0);
        $is_csv= $request->get('csv',0);
//        $dates=OrderDate::find()->where(['order_id'=>$order_id])->select('order_id,start_at,end_at')->asArray()->one();//获取订单投放时间段
        $datelist = OrderDate::getOrderDateSeries($order_id);//获取连续的时间
        //街道分页
        $street = OrderArea::getStreetsByOrderId($order_id);//获取街道
        $srr=array();
        if(!empty($street)){
            $streetArr = explode(',', $street['street_area']);
            $pageSize =20;
            $pages = new Pagination(['totalCount' => count($streetArr),'pageSize' => $pageSize]);
            $srr = array_slice($streetArr,$pages->offset,$pages->limit);
            //导出cvs
            if($is_csv==1){
                $csv=array();
                $newdate = OrderThrowOrderDate::findtrue($order_id,$streetArr,$datelist);
                $title = ['投放地区/排期'];
                $title = array_merge($title,$datelist);
                foreach ($newdate as $k=>$v){
                    $csv[$k]['area_id']=SystemAddress::getAreaNameById($k);
                    foreach($datelist as $kd=>$vd){
                        if(isset($v[$vd]) == 1){
                            $csv[$k]['playnum'.$kd]='播放';
                        }else{
                            $csv[$k]['playnum'.$kd]='无排期';
                        }
                    }
                }
                $file_name="投放地区/排期".date("mdHis",time()).".csv";
                ToolsClass::Getcsv($csv,$title,$file_name);
            }
        }
        $newdate =OrderThrowOrderDate::findtrue($order_id,$srr,$datelist);
        return $this->render('throwarea', [
            'pages' => $pages,
            'srr' => $srr,
            'datelist' => $datelist,
            'newdate' => $newdate,
            'order_id' => $order_id,
        ]);
    }




    private function actionOrderData($uid){
        $orderObj = new Order();
        $where = " member_id = $uid  AND  examine_status < 5  AND (payment_status = 1 or payment_status = 3) ";
        $orderData = $orderObj->getAllByUid($where);
        return $orderData;
    }

    public function actionGettranscoding()
    {
        $request = \Yii::$app->request;
        $fileid= $request->get('fileid','');
        $redisObj = \Yii::$app->redis;
        $redisObj->select(2);
        $url= $redisObj->get("transcoding:".$fileid);
        if(empty($url)){
            return json_encode(array('status'=>400));
        }else{
            return json_encode(array('status'=>200,'url'=>$url));
        }
    }

    /**
     * 广告素材上传规范
     * @return string
     */
    public function actionStandard(){
        return $this->render('standard');
    }

}
