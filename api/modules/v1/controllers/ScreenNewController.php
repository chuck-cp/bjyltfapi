<?php
namespace api\modules\v1\controllers;
use api\modules\v1\models\LogExamine;
use api\modules\v1\models\MemberEquipment;
use api\modules\v1\models\MemberTeam;
use api\modules\v1\models\ShopScreenReplace;
use api\modules\v1\models\ShopScreenReplaceList;
use api\modules\v1\models\SystemDevice;
use common\libs\ToolsClass;
use Yii;
use api\core\ApiController;
use api\modules\v1\models\Screen;
use api\modules\v1\models\Shop;
use api\modules\v1\models\ShopApply;
use yii\base\Exception;

/**
 * 安装认证
 */
class ScreenNewController extends ApiController
{
    public function behaviors()
    {
        //使用验证权限过滤器
        $behaviors = parent::behaviors();
        if(in_array($this->action->id,['existence','get','screennumber','post','activation'])){
            unset($behaviors['authenticator']);
        }
        return $behaviors;
    }
    /**
     * 安装验证
     */
    public function actionExistence(){
        $shopapplyModel =new ShopApply();
        if($result = $shopapplyModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $shopapplyid=$shopapplyModel->getExistence();//验证订单是否存在
        if(empty($shopapplyid))
        {
            return $this->returnData('ORDER_NOT_EXIST');
        }
        else
        {
            $shopModel=new Shop();
            $shop=$shopModel->getShopStatus($shopapplyid['id']);//获取订单状态是否安装完成
            if($shop['status']==2 or $shop['status']==3 or $shop['status']==4){ // 状态(0、申请待审核 1、申请未通过 2、待安装 3、安装待审核 4、安装未通过 5、已安装)
                return $this->returnData('SUCCESS',array('status'=>$shop['status'],'id'=>$shopapplyid['id']));
            }
            else
            {
                return $this->returnData('ALREADY_INSTALLED');//状态不对
            }
        }
    }
    /**
     * 获取订单详情
     */
    public function actionGet(){
        $shopapplyModel =new ShopApply();
        if($result = $shopapplyModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $shopapplyid=$shopapplyModel->getExistence();//验证订单是否存在
        if(empty($shopapplyid)){
            return $this->returnData('ORDER_NOT_EXIST');
        }else{
            if($shopapplyid['id']!=$shopapplyModel->id){
                return $this->returnData('ORDER_NOT_EXIST');
            }
        }
        $shop=$shopapplyModel->getShopOrder($shopapplyModel->id);//查询订单详情
        if(!empty($shop)){
            if($shop['status']==4){
                $logdata=LogExamine::find()->where(['and',['foreign_id'=>$shopapplyModel->id],['examine_result'=>2],['examine_key'=>4]])->select('examine_desc')->orderBy("id desc")->asArray()->one();
                if(empty($logdata)){
                    $logdata=array('examine_desc'=>"未找到审核失败原因");
                }
                $shop=array_merge($shop,$logdata);
            }
        }
        return $this->returnData('SUCCESS',$shop);
    }

    /**
     * 获取屏幕id
     */
    public function actionScreennumber($shop_id){
        $ScreenModel=new Screen();
        $Screen=$ScreenModel->getScteensId($shop_id);
        if(!empty($Screen)){
            return $this->returnData('SUCCESS',$Screen);
        }
        return $this->returnData('SCREEN_NUMBER_ERROR');
    }
    /**
     * 屏幕安装 安装人员线下获取订单详情
     */
    public function actionNumber($shop_id){
        $replace_id = Yii::$app->request->get('replace_id');
        //获取屏幕数量
        $ScreenModel=new Screen();
        $Screen=$ScreenModel->getScteens($shop_id,$replace_id);
        if(!empty($Screen)){
            return $this->returnData('SUCCESS',$Screen);
        }
        return $this->returnData('SCREEN_NUMBER_ERROR');
    }
    /**
     * 屏幕安装 安装反馈修改图片获取详情
     */
    public function actionScreenimgshow($shop_id){
        $replace_id = Yii::$app->request->get('replace_id') ?? 0;
        if(!$replace_id){ $replace_id=0; }
        $shopModel=new ShopApply();
        $shop=$shopModel->getScreenShopimginfo($shop_id, $replace_id);//获取店铺图片详情
        // 验证token 与数据是否 匹配
        if($replace_id > 0){
            $replaceInfo = ShopScreenReplace::findOne($replace_id);
            if($replaceInfo->install_member_id != Yii::$app->user->id){
                return $this->returnData('ERROR');
            }
            $shop['problem_description'] = $replaceInfo->problem_description;
        }
        if(empty($shop)){
            return $this->returnData('ERROR');
        }
        return $this->returnData('SUCCESS',$shop);
    }
    /**
     * 安装完成确认
     */
    public function actionPost(){
        $shopapplyModel =new ShopApply();
        if($result = $shopapplyModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $shopapplyid=$shopapplyModel->getExistence();//验证订单是否存在
        if(empty($shopapplyid))
        {
            return $this->returnData('ORDER_NOT_EXIST');
        }else{
            if($shopapplyid['id']!=$shopapplyModel->id){
                return $this->returnData('ORDER_NOT_EXIST');
            }
        }
        //获取待安装设备编码
        $screenModel=new Screen();
        $screenNumber=$screenModel->getScteensNumber($shopapplyModel->id);
        if(empty($screenNumber)){
            return $this->returnData('GET_SCREEN_FAIL');
        }
        $screenNumber = array_column($screenNumber,'software_number');
        //验证设备编码是否存在
        $images=json_decode($shopapplyModel->install_images);
        $deviceNum=[];//提交到综合事业部屏幕入库
        foreach($images as $key=>$value) {
            if(!in_array(trim($value->screen_number),$screenNumber)) {
                return $this->returnData('CODE_DOES_NOT_EXIST',array('id'=>$value->id));
            }else{
                //$deviceNum=$deviceNum.$value->screen_number.",";
                $deviceNum[] = $value->screen_number;
            }
        }
        //判断验证码是否正确
        if(!ToolsClass::checkVerify($shopapplyModel->install_mobile,$shopapplyModel->verify)){
            return $this->returnData('VERIFY_ERROR');
        }
        //更新屏幕安装数据
        $dbTrans =Yii::$app->db->beginTransaction();//事物开始
        try {

            //修改安装人
            $shopapplyModel::updateAll(['install_name'=>$shopapplyModel->install_name,'install_mobile'=>$shopapplyModel->install_mobile],['id'=>$shopapplyModel->id]);
            //修改安装位置
            foreach($images as $key=>$value){
                $screenModel::updateAll(['image'=>$value->image],['id'=>$value->id]);
            }
            //修改安装订单状态，改为安装确认
            $shopModel= Shop::find()->where(['id' =>$shopapplyModel->id])->select('id,status')->one();
            if($shopModel['status']==2 or $shopModel['status']==3)
            {
                $shopModel->status =3;
                $shopModel->save();
            }else{
                return $this->returnData('SHOP_STATUS_ERROR');
            }
            if(!$softDvArr = SystemDevice::checkIsOut($deviceNum)){
                return $this->returnData('SCREEN_NOT_EXIST'); //屏幕不合法
            }
            if(!Screen::storePost($shopapplyModel->id,$softDvArr)){
                return $this->returnData('ADD_SCREEN_TO_LIST_FAIL'); //新增屏幕入队列失败
            }
            $dbTrans->commit();
            return $this->returnData('SUCCESS');
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            $dbTrans->rollBack();
            return $this->returnData('ERROR');
        }
    }




    /**
     * 屏幕安装 安装人员线下获取订单详情
     */
    public function actionScreencheck(){
        $shopapplyModel =new ShopApply();
        if($result = $shopapplyModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $shop=$shopapplyModel->getShopOrder($shopapplyModel->id);//查询订单详情
        //数据为空出错
        if(empty($shop)){
            return $this->returnData('ERROR');
        }
        //安装状态
        $replaceStatus = ShopScreenReplace::find()->where(['id'=>$shopapplyModel->replace_id])->asArray()->one();
        $shop['status'] = $replaceStatus['status'];
        $shop['screen_status'] = $replaceStatus['screen_status'];
        if($shop['status']==3){
            $logdata=LogExamine::find()->where(['and',['foreign_id'=>$shopapplyModel->id],['examine_result'=>2],['examine_key'=>4]])->select('examine_desc')->orderBy("id desc")->asArray()->one();
            if(empty($logdata)){
                $logdata=array('examine_desc'=>"未找到审核失败原因");
            }
            $shop=array_merge($shop,$logdata);
        }
        return $this->returnData('SUCCESS',$shop);
    }
    public function actionChange(){
        $params = Yii::$app->request->get();
        //验证token是否正确，以及是否是当前登录用户
        if(!$member = MemberEquipment::checkTokenIsTrue($params['token'])){
            return $this->returnData('ERROR');
        }
        if($member->member_id != Yii::$app->user->id){
            return $this->returnData('ERROR');
        }
        //店铺信息
        $shop = (new ShopApply())->getShopOrder($params['id']);
        //换屏信息
        $replace = ShopScreenReplace::find()->where(['id'=>$params['replace_id']])->asArray()->one();
        //审核未通过原因
        if(!empty($replace) && $replace['status'] == 3){
            $logdata=LogExamine::find()->where(['and',['foreign_id'=>$params['replace_id']],['examine_result'=>2],['examine_key'=>8]])->select('examine_desc')->orderBy("id desc")->asArray()->one();
            if(!empty($logdata)){
                $replace['examine_desc'] = $logdata['examine_desc'];
            }else{
                $replace['examine_desc'] = '暂无驳回原因';
            }
        }
        return $this->returnData('SUCCESS',array_merge($shop,['replace'=>$replace]));

    }



    /*
     * 换屏信息
     */
    public function actionChangeScreen($replace_id){
        $replaceData = ShopScreenReplace::find()->where(['id'=>$replace_id, 'install_member_id'=>Yii::$app->user->id])->select('id,maintain_type,shop_id,status,replace_screen_number')->asArray()->one();
        if(!$replaceData){
            return $this->returnData('ERROR');
        }
        //查出现在有几块屏幕
        $screenNumber = Screen::find()->where(['shop_id'=>$replaceData['shop_id']])->count();
        $replaceData['currentNumber'] = $screenNumber;
        return $this->returnData('SUCCESS',$replaceData);

    }

    //屏幕新增
    public function actionScreenIncr(){
        try{
            $trans = Yii::$app->db->beginTransaction();
            //获取信息
            $shopapplyModel =new ShopApply();
            if($result = $shopapplyModel->loadParams($this->params,$this->action->id)){
                return $this->returnData($result);
            }
            //屏幕替换表
            $screenReplaceModel = new ShopScreenReplace();
            if($result = $screenReplaceModel->loadParams($this->params,$this->action->id)){
                return $this->returnData($result);
            }
            $ckre = ShopScreenReplace::checkIsInstallMember($screenReplaceModel->replace_id);
            if($ckre != 'SUCCESS'){
                return $this->returnData($ckre);
            }
            //验证屏幕编码是否 在库中
            $images=json_decode($shopapplyModel->install_images);
            if(empty($images)){
                return 'SCREEN_CAN_NOT_EMPTY';
            }
            $deviceNum = [];//提交到综合事业部屏幕入库
            foreach($images as $k=>$v){
                $deviceData=SystemDevice::find()->where(['and',['software_id'=>$v->screen_number],['is_output'=>1],['status'=>1]])->select('device_number')->asArray()->one();//查询出硬件编码，准备更新至屏幕表中
                if(empty($deviceData)){
                    return $this->returnData('CODE_DOES_NOT_HOUSE',array('id'=>$v->id));
                }else{
                    $images[$k]->number=$deviceData['device_number'];//入screen硬件编码
                    $images[$k]->name="屏幕".($k+1);
                    $deviceNum[$k]['deviceNum']=$v->screen_number;//入屏幕管理平台软件编码
                    $deviceNum[$k]['realNum']=$deviceData['device_number'];//入屏幕管理平台硬件编码
                    $deviceNum[$k]['size'] = '0';
                }
            }
            //屏幕存储到屏幕表中
            foreach($images as $k=>$v){
                $screen=new Screen();
                $screen->shop_id = $shopapplyModel->id;
                $screen->number = SystemDevice::getDevice($v->screen_number);//硬件编码
                $screen->software_number = $v->screen_number;
                $screen->name = '';
                $screen->image = $v->image;
                $screen->replace_id = $screenReplaceModel->replace_id;
                $screen->save();
            }
            //存储到yl_shop_screen_replace表中
            if('SUCCESS' != $screenReplaceModel->screenOperate(4)){
                throw new Exception('屏幕替换表写入失败！');
            }
            //新增设备写入reids
            if(!Screen::storePost($shopapplyModel->id, $deviceNum, $screenReplaceModel->replace_id)){
                throw new Exception('新增屏幕写入队列失败！');
            }
            $trans->commit();
            return $this->returnData('SUCCESS');
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            $trans->rollBack();
            return $this->returnData('ERROR');
        }
    }

    public function actionChangePost(){
        $params = $this->params;
        if(!$params['replace_id']){
            return $this->returnData();
        }
        $ckre = ShopScreenReplace::checkIsInstallMember($params['replace_id']);
        if($ckre != 'SUCCESS'){
            return $this->returnData($ckre);
        }
        if(isset($params['change']) && $params['change'] == 'change'){
            //如果是激活失败重新更换屏幕
            $result = (new Screen())->changeFailPost($params);
            return $this->returnData($result);
        }
        $solfts = json_decode($params['install_images'],true);
        if(empty($solfts)){
            return $this->returnData('ERROR');
        }
        $software_number = [];
        $replace_list = [];
        $delArr = [];
        foreach ($solfts as $v){
            //入屏幕管理平台软件编码
            $software_number[] = $v['screen_number'];
            //更换列表
            $replace_list[] = $v['id'];
            //要从综合事业部删除的屏幕（软件）
            $delArr[] = ShopScreenReplaceList::getSolft($v['id']);
        }
        //1.验证屏幕是否在device表中出库,并获取设备硬件编号

        if(!$device_numbers = SystemDevice::checkIsOut($software_number)){
            return $this->returnData('SCREEN_NOT_EXIST');
        }
        //2.将旧的设备从综合事业部删除、添加新的设备到综合事业部（即为店铺更换屏幕过程）

        //2-2:添加新的设备
        if(!Screen::storePost($params['id'],$device_numbers)){
            return $this->returnData('ADD_SCREEN_TO_LIST_FAIL'); //新增屏幕入队列失败
        }
        $dbTrans = Yii::$app->db->beginTransaction();
        try{
            //写入队列删除
            if(!Screen::screenRedisOperate('delete',implode(',',$delArr))){
                throw new Exception('删除的屏幕写入队列失败');
            }
            //3.若第二步成功则从screen表里删除掉原店铺里需要更换的设备，为该店铺增加新的设备
            $screenModel = new Screen();
            foreach ($replace_list as $k => $v){
                //查找硬件编号
                $number = ShopScreenReplaceList::findOne($v)->device_number;
                //删除
                $del = Screen::find()->where(['number'=>$number])->one();
                if($del){
                    $del->delete();
                }
                //添加
                $smodel = clone $screenModel;
                $smodel->shop_id =  $params['id'];
                $smodel->replace_id = $params['replace_id'];
                $smodel->software_number = $solfts[$k]['screen_number'];
                $smodel->name = '屏幕'.($k+1);
                $smodel->image = $solfts[$k]['image'];
                $smodel->number = $device_numbers[$k]['realNum'];
                $smodel->save();
            }
            //4.屏幕更换列表里写入新的设备
            foreach ($solfts as $k => $v){
                $replaceListModel = ShopScreenReplaceList::findOne($v['id']);
                $replaceListModel->replace_device_number = $device_numbers[$k]['realNum'];
                $replaceListModel->save();
            }
            //5.更新replace状态
            ShopScreenReplace::updateAll(['status'=>2],['id'=>$params['replace_id']]);
            $dbTrans->commit();
            return $this->returnData('SUCCESS');
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            //print_r($e->getMessage());exit;
            $dbTrans->rollBack();
            return $this->returnData('ERROR');
        }
    }
    /**
     * 屏幕安装  安装激活状态
     */
    public function actionScreenstatus($shop_id){
        $replace_id = Yii::$app->request->get('replace_id');
        $shopModel=new Shop();
        if(!$replace_id){
            $shop=$shopModel->getShopStatus($shop_id);//获取订单状态是否安装完成
            if($shop['status']==3 or $shop['status']==5){
                $screenModel=new Screen();
                if($screenModel->getActivation($shop_id)){
                    return $this->returnData('SUCCESS',array('status'=>true));
                }else{
                    return $this->returnData('SUCCESS',array('status'=>false));
                }
            }else{

            }
        }else{
            $replace = ShopScreenReplace::find()->where(['id'=>$replace_id])->asArray()->one();
            if(empty($replace_id)){
                return $this->returnData('ERROR');
            }
            if($replace['screen_status'] == 1){
                return $this->returnData('SUCCESS',array('status'=>true));
            }else{
                return $this->returnData('SUCCESS',array('status'=>false));
            }
        }

    }
    /**
     * 屏幕安装 获取屏幕安装失败的编码
     */
    public function actionScreenactivation(){
        $shopapplyModel =new ShopApply();
        if($result = $shopapplyModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $screenModel=new Screen();
        $screenData=$screenModel->getScreenactivation($shopapplyModel->id);
        if(isset($this->params['change'])){
            $screenData['team'] = MemberTeam::getInstallInfo(0,Yii::$app->user->id);
        }else{
            if($screenData['install_member_id']!=Yii::$app->user->id){
                return $this->returnData('ERROR');
            }
            if(empty($screenData)){
                return $this->returnData('SUCCESS');
            }
        }
        return $this->returnData('ERROR',$screenData);
    }
    /**
     * 屏幕安装 安装反馈修改图片获取详情
     */
//    public function actionScreenimgshow($shop_id){
//        $shopModel=new ShopApply();
//        $shop=$shopModel->getScreenShopimginfo($shop_id);//获取店铺图片详情
//        if(empty($shop)){
//            return $this->returnData('ERROR');
//        }
//        return $this->returnData('SUCCESS',$shop);
//    }
    /*
     * 屏幕更换未通过
     */
    public function actionChangeUpdate($shop_id){
        $replace_id = Yii::$app->request->get('replace_id');
        $replaceData = ShopScreenReplace::find()->where(['id'=>$replace_id, 'install_member_id'=>Yii::$app->user->id, 'status'=>3])->count();
        if(!$replaceData){
            return $this->returnData('ERROR');
        }
        //获取需要修改的屏幕信息
        $updateList = Screen::find()->where(['replace_id'=>$replace_id])->asArray()->all();
        $ckre = ShopScreenReplace::checkIsInstallMember($replace_id,'replace',false);
        if($ckre != 'SUCCESS'){
            return $this->returnData($ckre);
        }
        if(empty($updateList)){
            return $this->returnData('UPDATE_SCREEN_NUMBER_ERROR');
        }
        //安装人信息
        $install = MemberTeam::getInstallInfo($replace_id);
        return $this->returnData('SUCCESS',['replace'=>$updateList,'install_member'=>$install]);
    }
    /*
     * 屏幕更换未通过提交修改数据
     */
    public function actionChangeNotPassUpdate(){
        $params = $this->params;
        $replaceObj = ShopScreenReplace::findOne($params['replace_id']);
        $ckre = ShopScreenReplace::checkIsInstallMember($params['replace_id']);
        if($ckre != 'SUCCESS'){
            return $this->returnData($ckre);
        }
        if(!$replaceObj){
            return $this->returnData('ERROR');
        }
        if($replaceObj->install_member_id != Yii::$app->user->id || $replaceObj->status != 3){
            return $this->returnData('ERROR');
        }
        $trans = Yii::$app->db->beginTransaction();
        try{
            //该状态
            $replaceObj->status = 2;
            $replaceObj->save();
            //更换screent图片
            $screenData = json_decode($params['install_images'], true);
            if(empty($screenData)){
                return $this->returnData('ERROR');
            }
            //$screenModel = new Screen();
            foreach ($screenData as $k => $v){
                $screenModel = Screen::findOne($v['id']);
                $screenModel->image  = $v['image'];
                $screenModel->save();
            }
            $trans->commit();
            return $this->returnData('SUCCESS');
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            $trans->rollBack();
            return $this->returnData('ERROR');
        }

    }
    /**
     * 屏幕安装 安装反馈修改图片保存
     */
    public function actionScreenimgupdate(){
        $shopapplyModel =new ShopApply();
        if($result = $shopapplyModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        // 更新屏幕安装图片数据
        $dbTrans =Yii::$app->db->beginTransaction();//事务开始
        try {
            //更新图片
            $images=json_decode($shopapplyModel->install_images);
            foreach($images as $k=>$v){
                $screensoftware=Screen::find()->where(['id'=>$v->id])->select('id,image')->one();
                $screensoftware->image =$v->image;
                $screensoftware->save();
            }
            //修改安装订单状态，改为安装确认
            $shopModel= Shop::find()->where(['id' =>$shopapplyModel->id])->select('id,status,examine_number,last_examine_user_id,install_member_id')->one();
            // 验证token 与数据是否 匹配
//            if($shopModel['install_member_id']!=Yii::$app->user->id){
//                return $this->returnData('ERROR');
//            }
            if($shopModel['status']==4){
                $shopModel->status =3;
                $shopModel->examine_number=0;
                $shopModel->last_examine_user_id=0;
                $shopModel->save();
            }else{
                return $this->returnData('SHOP_STATUS_ERROR');
            }
            $dbTrans->commit();
            return $this->returnData('SUCCESS');
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            $dbTrans->rollBack();
            return $this->returnData('ERROR');
        }
    }






}