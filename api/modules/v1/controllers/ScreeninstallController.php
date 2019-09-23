<?php
namespace api\modules\v1\controllers;
use api\modules\v1\models\BuildingShopFloor;
use api\modules\v1\models\BuildingShopPark;
use api\modules\v1\models\LogExamine;
use api\modules\v1\models\Member;
use api\modules\v1\models\MemberEquipment;
use api\modules\v1\models\MemberTeam;
use api\modules\v1\models\ShopScreenAdvertMaintain;
use api\modules\v1\models\ShopScreenReplace;
use api\modules\v1\models\ShopScreenReplaceList;
use api\modules\v1\models\SystemDevice;
use api\modules\v1\models\ShopHeadquarters;
use common\libs\PublicClass;
use common\libs\Redis;
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
class ScreeninstallController extends ApiController
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
        $ckre = ShopScreenReplace::checkIsInstallMember($shopapplyModel->id,'shop');
        if($ckre != 'SUCCESS'){
            return $this->returnData($ckre);
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
     * 验证屏幕是否激活
     */
    public function actionActivation(){
        $shopapplyModel =new ShopApply();
        if($result = $shopapplyModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $shopapplyid=$shopapplyModel->getExistence();//验证订单是否存在
        if(empty($shopapplyid)){
            return $this->returnData('ORDER_NOT_EXIST',1);
        }else{
            if($shopapplyid['id']!=$shopapplyModel->id){
                return $this->returnData('ORDER_NOT_EXIST',2);
            }
        }
        $screenModel=new Screen();
        if($screenModel->getActivation($shopapplyModel->id)){
            return $this->returnData('SUCCESS');
        }
        return $this->returnData('ERROR');
    }

    public function actionActivmq(){
        return $this->returnData('ERROR');
    }

    /**
     * 线下安装获取数量
     */
    public function actionScreennumberunline($shop_id){
        //验证权限
        //$this->isInsideAction();//是否为内部人员
        //获取屏幕数量
        $ScreenModel=new Screen();
        $Screen=$ScreenModel->getScteensNumberunline($shop_id);
        if(!empty($Screen)){
            return $this->returnData('SUCCESS',$Screen);
        }
        return $this->returnData('SCREEN_NUMBER_ERROR');
    }
    /**
     * 线下安装保存安装信息
     */
    public function actionUnline(){
        //验证权限
        //$this->isInsideAction();//是否为内部人员
        //获取信息
        $shopapplyModel =new ShopApply();
        if($result = $shopapplyModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $ckre = ShopScreenReplace::checkIsInstallMember($shopapplyModel->id,'shop');
        if($ckre != 'SUCCESS'){
            return $this->returnData($ckre);
        }
        //验证 token 是否正确
        $memberData=MemberEquipment::find()->where(['token'=>$shopapplyModel->token])->select('member_id')->asArray()->one();
        $shopmember=Shop::find()->where(['id'=>$shopapplyModel->id])->select('member_id')->asArray()->one();
        if($memberData['member_id']!=$shopmember['member_id']){
            return $this->returnData('ERROR');
        }
        //验证屏幕编码是否 在库中
        $images=json_decode($shopapplyModel->install_images);
        $deviceNum = [];//提交到综合事业部屏幕入库
        foreach($images as $k=>$v){
            $deviceData=SystemDevice::find()->where(['and',['software_id'=>$v->screen_number],['is_output'=>1],['status'=>1]])->select('device_number')->asArray()->one();//查询出硬件编码，准备更新至屏幕表中
            if(empty($deviceData)){
                return $this->returnData('CODE_DOES_NOT_HOUSE',array('id'=>$v->id));
            }else{
                $images[$k]->number=$deviceData['device_number'];//硬件编码
                $images[$k]->name="屏幕".($k+1);
                $deviceNum[] = str_replace(' ','',$v->screen_number);
            }
        }
        //验证屏幕编码是否 在屏幕表中
        foreach($images as $k=>$v){
            $screenData=Screen::find()->where(['software_number'=>$v->screen_number])->select('shop_id')->asArray()->one();
            if(!empty($screenData)){
                return $this->returnData('CODE_DOES_NOT_EXIST',array('id'=>$v->id));
            }
        }

        //判断验证码是否正确
        if(!ToolsClass::checkVerify($shopapplyModel->install_mobile,$shopapplyModel->verify)){
            return $this->returnData('VERIFY_ERROR');
        }

        // 更新屏幕安装数据
        $dbTrans =Yii::$app->db->beginTransaction();//事物开始
        try {
            $redis = Redis::getInstance(4);
            if($shopapplyModel->isupdate!="false"){//标识是否为更新
                $screendata=explode(',',$shopapplyModel->isupdate);//
                foreach($screendata as $k=>$v){
                    $screensoftware=Screen::find()->where(['id'=>$v])->select('software_number')->one();
                    $software_number[]=$screensoftware['software_number'];
                    $deviceModel= SystemDevice::find()->where(['software_id' =>$software_number])->one();
                    $deviceModel->status =2;
                    $deviceModel->save();
                    Screen::findOne($v)->delete();//从屏幕表中删除该记录
                }
                //写入队列
                if(!Screen::screenRedisOperate('delete',implode(',',$software_number))){
                    throw new Exception('删除的屏幕写入队列失败');
                }
            }
            //屏幕存储到屏幕表中
            foreach($images as $k=>$v){
                $screen=new Screen();
                $screen->shop_id=$shopapplyModel->id;
                $screen->number=$v->number;//硬件编码
                $screen->software_number=$v->screen_number;
                $screen->name=$v->name;
                $screen->image=$v->image;
                $screen->save();
            }
            //修改安装人
            $shopapplyModel::updateAll(['install_name'=>$shopapplyModel->install_name,'install_mobile'=>$shopapplyModel->install_mobile],['id'=>$shopapplyModel->id]);
            //修改安装订单状态，改为安装确认
            $shopModel= Shop::find()->where(['id' =>$shopapplyModel->id])->select('id,status')->one();
            if($shopModel['status']==2 or $shopModel['status']==3)
            {
                $shopModel->status =3;
                $shopModel->save();
            }else{
                return $this->returnData('SHOP_STATUS_ERROR');
            }
            //提交设备入库

            $dbTrans->commit();
            return $this->returnData('SUCCESS');
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            $dbTrans->rollBack();
            return $this->returnData('ERROR');
        }


    }

    /**
     * 线下验证屏幕是否激活
     */
    public function actionActivationunline(){
        //$this->isInsideAction();//判断是否是内部人员
        $shopapplyModel =new ShopApply();
        if($result = $shopapplyModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        //验证 token 是否正确
        $memberData=MemberEquipment::find()->where(['token'=>$shopapplyModel->token])->select('member_id')->asArray()->one();
        $shopmember=Shop::find()->where(['id'=>$shopapplyModel->id])->select('member_id')->asArray()->one();

        if($memberData['member_id']!=$shopmember['member_id']){
            return $this->returnData('ERROR');
        }
        $screenModel=new Screen();
        $screenData=$screenModel->getActivationunline($shopapplyModel->id);
        if(empty($screenData)){
            return $this->returnData('SUCCESS');
        }
        return $this->returnData('ERROR',$screenData);
    }
    /**
     * 线下获取订单详情
     */
    public function actionUnderlinecheck(){
        //$this->isInsideAction();//判断是否是内部人员
        $shopapplyModel =new ShopApply();
        if($result = $shopapplyModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $shop=$shopapplyModel->getShopOrder($shopapplyModel->id);//查询订单详情
        if(empty($shop)){
            return $this->returnData('ERROR');
        }
        // 验证token 与数据是否 匹配
//        if($shop['member_id'] != Yii::$app->user->id){
//            return $this->returnData('ERROR');
//        }
        if(!empty($shop)){
            if($shop['status']==1){
                $logdata=LogExamine::find()->where(['and',['foreign_id'=>$shopapplyModel->id],['examine_result'=>2],['examine_key'=>1]])->select('examine_desc')->orderBy("id desc")->asArray()->one();
                if(empty($logdata)){
                    $logdata=array('examine_desc'=>"未找到审核失败原因");
                }
                $shop=array_merge($shop,$logdata);
            }
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
     * 线下安装订单状态
     */
    public function actionUnderlinestatus($shop_id){
        //$this->isInsideAction();//判断是否是内部人员
        $shopModel=new Shop();
        $shop=$shopModel->getShopStatus($shop_id);//获取订单状态是否安装完成
        if($shop['status']==3 or $shop['status']==5){
            $screenModel=new Screen();
            if($screenModel->getActivation($shop_id)){
                return $this->returnData('SUCCESS',array('status'=>true));
            }else{
                return $this->returnData('SUCCESS',array('status'=>false));
            }
        }else{
            return $this->returnData('ERROR');
        }
    }
    /**
     *  普通安装和内部安装共用安装反馈修改图片获取详情
     */
    public function actionUnderlineimgshow($shop_id){
        //  $this->isInsideAction();//判断是否是内部人员
        $shopModel=new ShopApply();
        $shop=$shopModel->getShopimginfo($shop_id);//获取店铺图片详情
        if(empty($shop)){
            return $this->returnData('ERROR');
        }
        return $this->returnData('SUCCESS',$shop);
    }
    /**
     * 普通安装和内部安装共用修改图片安装反馈修改图片保存
     */
    public function actionUnderlineimgupdate(){
        //验证权限
        $this->isInsideAction();//是否为内部人员
        //获取信息
        $shopapplyModel =new ShopApply();
        if($result = $shopapplyModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        //验证 token 是否正确
        $memberData=MemberEquipment::find()->where(['token'=>$shopapplyModel->token])->select('member_id')->asArray()->one();
        $shopmember=Shop::find()->where(['id'=>$shopapplyModel->id])->select('member_id')->asArray()->one();
        if($memberData['member_id']!=$shopmember['member_id']){
            return $this->returnData('ERROR');
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
            $shopModel= Shop::find()->where(['id' =>$shopapplyModel->id])->select('id,status,examine_number,last_examine_user_id')->one();
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
    /**
     * 屏幕安装 安装人员待安装的店铺列表  ->joinWith('shopInfo')
     */
    public function actionScreenshoplist(){
        if (\Yii::$app->user->identity->quit_status == 1) {
            // 该用户已离职
            return $this->returnData('MEMBER_QUIT');
        }
        //店铺列表
        $shopData = [];
        $shopData = Shop::getShopMainList();
        //更换屏幕店铺列表
        $replace = ShopScreenReplace::getReplaceMiantainList();
        //20190529新加线下广告维护
        $advertMaintain = ShopScreenAdvertMaintain::getAdvertMaintainList();
        //20190619新加海报led公园或写字楼申请
        $buildParkMaintainList = array_merge(BuildingShopFloor::getMyBuildTaskList(), BuildingShopPark::getMyParkTaskList());
        $total = array_merge($shopData, $replace, $advertMaintain, $buildParkMaintainList);
        array_multisort(array_column($total,'tm'),SORT_DESC,$total);
        return $this->returnData('SUCCESS',$total);

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
        if(empty($replaceStatus)){
            $shop['maintain_type'] = 1;
            if($shop['status'] == 4){
                $logdata=LogExamine::find()->where(['and',['foreign_id'=>$shopapplyModel->id],['examine_result'=>2],['examine_key'=>4]])->select('examine_desc')->orderBy("id desc")->asArray()->one();
                if(empty($logdata)){
                    $logdata=array('examine_desc'=>"未找到审核失败原因");
                }
                $shop=array_merge($shop,$logdata);
            }
        }else{
            $shop['maintain_type'] = $replaceStatus['maintain_type'];
            $shop['status'] = $replaceStatus['status'];
            $shop['screen_status'] = $replaceStatus['screen_status'];
            if($shop['status'] == 3){
                $logdata=LogExamine::find()->where(['and',['foreign_id'=>$shopapplyModel->replace_id],['examine_result'=>2],['examine_key'=>4]])->select('examine_desc')->orderBy("id desc")->asArray()->one();
                if(empty($logdata)){
                    $logdata=array('examine_desc'=>"未找到审核失败原因");
                }
                $shop=array_merge($shop,$logdata);
            }
        }

        return $this->returnData('SUCCESS',$shop);
    }
    public function actionChange(){
        $params = Yii::$app->request->get();
        $ckre = ShopScreenReplace::checkIsInstallMember($params['replace_id'],'replace',false);
        if($ckre != 'SUCCESS'){
            return $this->returnData($ckre);
        }
        //店铺信息
        $shop = (new ShopApply())->getShopOrder($params['id']);
        //换屏信息
        $replace = ShopScreenReplace::find()->where(['id'=>$params['replace_id']])->select('install_device_number,install_software_number,remove_device_number,status,screen_status,problem_description')->asArray()->one();
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
    /**
     * 屏幕安装 安装人员线下获取订单详情
     */
    public function actionNumber($shop_id){
        // 验证token 与数据是否 匹配
        $shopData= Shop::find()->where(['and',['id'=>$shop_id],['install_member_id'=>Yii::$app->user->id]])->select('id')->asArray()->one();
        if(empty($shopData)){
            return $this->returnData('ERROR');
        }
        //获取屏幕数量
        $ScreenModel=new Screen();
        $Screen=$ScreenModel->getScteens($shop_id);
        if(!empty($Screen)){
            return $this->returnData('SUCCESS',$Screen);
        }
        return $this->returnData('SCREEN_NUMBER_ERROR');
    }
    /*
     * 换屏信息
     */
    public function actionChangeScreen($replace_id){
        $replaceData = ShopScreenReplace::find()->where(['id'=>$replace_id, 'install_member_id'=>Yii::$app->user->id])->select('problem_description,id,maintain_type,shop_id,status,replace_screen_number')->asArray()->one();
        if(!$replaceData){
            return $this->returnData('ERROR');
        }
        //查出现在有几块屏幕
        $screenNumber = Screen::find()->where(['shop_id'=>$replaceData['shop_id']])->count();
        $replaceData['currentNumber'] = $screenNumber;
        return $this->returnData('SUCCESS',$replaceData);

    }
    /**
     * 屏幕安装 保存屏幕安装信息
     */
    public function actionScreeninster(){
        //获取信息
        $shopapplyModel =new ShopApply();
        if($result = $shopapplyModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $ckre = ShopScreenReplace::checkIsInstallMember($shopapplyModel->id,'shop');
        if($ckre != 'SUCCESS'){
            return $this->returnData($ckre);
        }
        //验证屏幕编码是否 在库中
        $images=json_decode($shopapplyModel->install_images);
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
                $deviceNum[$k]['size'] = 0;
            }
        }
        //验证屏幕编码是否 在屏幕表中
        foreach($images as $k=>$v){
            $screenData=Screen::find()->where(['software_number'=>$v->screen_number])->select('shop_id')->asArray()->one();
            if(!empty($screenData)){
                return $this->returnData('CODE_DOES_NOT_EXIST',array('id'=>$v->id));
            }
        }
        //验证安装人是否存在
       $shopData= Shop::find()->where(['id'=>$shopapplyModel->id])->select('install_member_id,install_member_name,install_mobile')->asArray()->one();
       if(!empty($shopData)){
           // 验证token 与数据是否 匹配
              if($shopData['install_member_id']!=Yii::$app->user->id){
                  return $this->returnData('ERROR');
              }
       }else{
           return $this->returnData('ERROR');
       }
        // 更新屏幕安装数据
        $dbTrans =Yii::$app->db->beginTransaction();//事物开始
        try {
            if($shopapplyModel->isupdate!="false"){
                //标识是否为更新
                $screendata=explode(',',$shopapplyModel->isupdate);//
                foreach($screendata as $k=>$v){
                    $screensoftware=Screen::find()->where(['id'=>$v])->select('software_number')->one();
                    $software_number[] = $screensoftware['software_number'];
                    $deviceModel= SystemDevice::find()->where(['software_id' =>$software_number])->one();
                    $deviceModel->status =2;
                    $deviceModel->save();
                    Screen::findOne($v)->delete();//从屏幕表中删除该记录
                }
                //写入队列
                if(!Screen::screenRedisOperate('delete',implode(',',$software_number))){
                    throw new Exception('删除的屏幕写入队列失败');
                }
            }

            //屏幕存储到屏幕表中
            foreach($images as $k=>$v){
                $screen=new Screen();
                $screen->shop_id=$shopapplyModel->id;
                $screen->number=$v->number;//硬件编码
                $screen->software_number=$v->screen_number;
                $screen->name=$v->name;
                $screen->image=$v->image;
                $screen->save();
            }
            //修改安装订单状态，改为安装确认
            $shopModel= Shop::find()->where(['id' =>$shopapplyModel->id])->select('id,status,')->one();
            if($shopModel['status']==2 or $shopModel['status']==3){
                $shopModel->status =3;
                $shopModel->save();
            }else{
                return $this->returnData('SHOP_STATUS_ERROR');
            }
            if(!Screen::storePost($shopapplyModel->id,$deviceNum)){
                return $this->returnData('ADD_SCREEN_TO_LIST_FAIL'); //新增屏幕入队列失败
            }
            $dbTrans->commit();
            return $this->returnData('SUCCESS');

        } catch (Exception $e) {
            Yii::error($e->getMessage(),'db');
            $dbTrans->rollBack();
            return $this->returnData('ERROR');
        }
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
            $ckre = ShopScreenReplace::checkIsInstallMember($screenReplaceModel->id);
            if($ckre != 'SUCCESS'){
                return $this->returnData($ckre);
            }
            //验证屏幕编码是否 在库中
            $images=json_decode($shopapplyModel->install_images);
            if(empty($images)){
                return 'SCREEN_CAN_NOT_EMPTY';
            }
            $deviceNum=array();//提交到综合事业部屏幕入库
            foreach($images as $k=>$v){
                $deviceData=SystemDevice::find()->where(['and',['software_id'=>$v->screen_number],['is_output'=>1],['status'=>1]])->select('device_number')->asArray()->one();//查询出硬件编码，准备更新至屏幕表中
                if(empty($deviceData)){
                    return $this->returnData('CODE_DOES_NOT_HOUSE',array('id'=>$v->id));
                }else{
                    $images[$k]->number=$deviceData['device_number'];//入screen硬件编码
                    $images[$k]->name="屏幕".($k+1);
                    $deviceNum[$k]['deviceNum']=$v->screen_number;//入屏幕管理平台软件编码
                    $deviceNum[$k]['realNum']=$deviceData['device_number'];//入屏幕管理平台硬件编码
                    $deviceNum[$k]['size']='0';
                }
            }
            //验证屏幕编码是否 在屏幕表中
            foreach($images as $k=>$v){
                $screenData=Screen::find()->where(['software_number'=>$v->screen_number])->select('shop_id')->asArray()->one();
                if(!empty($screenData)){
                    return $this->returnData('CODE_DOES_NOT_EXIST',array('id'=>$v->id));
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
            //if(!Screen::screenRedisOperate('create',$deviceNum)){
            if(!Screen::storePost($shopapplyModel->id,$deviceNum,$screenReplaceModel->replace_id)){
                throw new Exception('新增屏幕写入队列失败！');
            }
            $trans->commit();
            return $this->returnData('SUCCESS');
        }catch(Exception $e){
            //print_r($e->getMessage());exit;
            Yii::error($e->getMessage(),'db');
            $trans->rollBack();
            return $this->returnData('ERROR');
        }
    }
    /**
     * 更换屏幕时
     * 综合事业部删除旧的，增加新的
     * screen删除旧的，增加新的
     * 更改shop_screen_repalce_list设备编号
     * 更改replace状态
     */
    public function actionChangePostNew(){
        $replaceModel = new ShopScreenReplace();
        if($reslut = $replaceModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($reslut);
        }
        $ckre = ShopScreenReplace::checkIsInstallMember($replaceModel->id);
        if($ckre != 'SUCCESS'){
            return $this->returnData($ckre);
        }
        $trans = Yii::$app->db->beginTransaction();
        try{
            if($replaceModel->install_software_number){
                $screenModel = new Screen();
                $screenModel->loadParams($this->params,$this->action->id);
                //1屏幕表（增删）
                $resScreen = $screenModel->screenInOut(2);
                if($resScreen != 'SUCCESS'){
                    return $this->returnData($resScreen); //屏幕表操作失败
                }
            }
            //2屏幕替换表
            $resReplace = $replaceModel->screenOperate(2);
            if($resReplace != 'SUCCESS'){
                return $this->returnData($resReplace); //屏幕表操作失败
            }
            $trans->commit();
            return $this->returnData('SUCCESS');
        }catch (Exception $e) {
            //print_r($e->getMessage());exit;
            Yii::error($e->getMessage(), 'db');
            $trans->rollBack();
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
            if($screenData['install_member_id'] != Yii::$app->user->id){
                return $this->returnData('ERROR');
            }
            if(empty($screenData)){
                return $this->returnData('SUCCESS');
            }
        }
        return $this->returnData('ERROR',$screenData);
        //return $this->returnData('SUCCESS',$screenData);
    }
    /**
     * 屏幕安装 安装反馈修改图片获取详情
     */
    public function actionScreenimgshow($shop_id){
        $shopModel=new ShopApply();
        $shop=$shopModel->getScreenShopimginfo($shop_id);//获取店铺图片详情
//        // 验证token 与数据是否 匹配
//        if($shop['install_member_id']!=Yii::$app->user->id){
//            return $this->returnData('ERROR');
//        }
        if(empty($shop)){
            return $this->returnData('ERROR');
        }
        return $this->returnData('SUCCESS',$shop);
    }

    /*
     * 屏幕更换未通过
     */
    public function actionChangeUpdate($shop_id){
        $replace_id = Yii::$app->request->get('replace_id');
        $replaceData = ShopScreenReplace::find()->where(['id'=>$replace_id, 'install_member_id'=>Yii::$app->user->id, 'status'=>3])->count();
        $ckre = ShopScreenReplace::checkIsInstallMember($replace_id,'replace',false);
        if($ckre != 'SUCCESS'){
            return $this->returnData($ckre);
        }
        if(!$replaceData){
            return $this->returnData('ERROR');
        }
        //获取需要修改的屏幕信息
        $updateList = Screen::find()->where(['replace_id'=>$replace_id])->asArray()->all();
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
                        //change-not-pass-update
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
        if($shopapplyModel->replace_id){
            $ckre = ShopScreenReplace::checkIsInstallMember($shopapplyModel->replace_id);
            if($ckre != 'SUCCESS'){
                return $this->returnData($ckre);
            }
        }else{
            $ckre = ShopScreenReplace::checkIsInstallMember($shopapplyModel->id,'shop');
            if($ckre != 'SUCCESS'){
                return $this->returnData($ckre);
            }
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
            if(!$shopapplyModel->replace_id){
                $shopModel= Shop::find()->where(['id' =>$shopapplyModel->id])->select('id,status,examine_number,last_examine_user_id,install_member_id')->one();
                if($shopModel['status']==4){
                    $shopModel->status =3;
                    $shopModel->examine_number=0;
                    $shopModel->last_examine_user_id=0;
                    $shopModel->save();
                }else{
                    return $this->returnData('SHOP_STATUS_ERROR');
                }
            }else{
                $replaceModel = ShopScreenReplace::findOne($shopapplyModel->replace_id);
                if(!$replaceModel){
                    return 'REPLACE_SHOP_NOT_FOUND';
                }
                $replaceModel->status = 2;
                $replaceModel->save();
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
     * 内部安装 获取总店详情
     */
    public function actionHeadoffice(){
        $shopHeadoffice=new ShopHeadquarters();
        if($result = $shopHeadoffice->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $shopHeadData=$shopHeadoffice->getShopHeadInfo($shopHeadoffice->id);//查询总店详情
        // 验证token 与数据是否 匹配
        if($shopHeadData['member_id']!=Yii::$app->user->id){
            return $this->returnData('ERROR');
        }
        if(empty($shopHeadData)){
            return $this->returnData('ERROR');
        }
        if($shopHeadData['examine_status']==2){
            $logdata=LogExamine::find()->where(['and',['foreign_id'=>$shopHeadoffice->id],['examine_result'=>2],['examine_key'=>7]])->select('examine_desc')->orderBy("id desc")->asArray()->one();
            if(empty($logdata)){
                $logdata=array('examine_desc'=>"未找到审核失败原因");
            }
            $shopHeadData=array_merge($shopHeadData,$logdata);
        }
        return $this->returnData('SUCCESS',$shopHeadData);
    }


    /**
     * 拆屏时获取核对信息
     * @return array
     */
    public function actionRemoveScreenInfo(){
        //店铺名称、地址、业务合作人、电话、拆除数量
        $shopScreenModel = new ShopScreenReplace();
        if($result = $shopScreenModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $replaceInfo = $shopScreenModel->getRemoveInfo();
        if(!$replaceInfo){
            return $this->returnData('ERROR');
        }
        return $this->returnData('SUCCESS',$replaceInfo);

    }

    /**
     * 拆屏时验证屏幕
     * @return array
     */
    public function actionRemoveScreenCheck(){
        $params = $this->params;
        $del = $params['remove_device_number'];
        $shop_id = $params['shop_id'];
        if(!empty($del)){
            $delErr = [];
            foreach ($del as $v){
                if(!Screen::find()->where(['shop_id'=>$shop_id, 'number'=>$v])->one()){
                    $delErr[] = $v;
                }
            }
            if(!empty($delErr)){
                return $this->returnData('DEL_DEVICE_NOT_FOUND',$delErr);
            }
        }
        $shopScreenModel = new ShopScreenReplace();
        if($result = $shopScreenModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $ckre = ShopScreenReplace::checkIsInstallMember($shopScreenModel->id);
        if($ckre != 'SUCCESS'){
            return $this->returnData($ckre);
        }
        $screenModel = new Screen();
        if($result = $screenModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $trans = Yii::$app->db->beginTransaction();
        try{
            //1.屏幕替换表
            $resReplace = $shopScreenModel->screenOperate(3);
            if($resReplace != 'SUCCESS'){
                return $this->returnData($resReplace); //屏幕表操作失败
            }
            //2.屏幕表（审核之后删除？）
            $trans->commit();
            return $this->returnData('SUCCESS');
        }catch (Exception $e){
            Yii::error($e->getMessage());
            $trans->rollBack();
            return $this->returnData('ERROR');
        }

    }


}