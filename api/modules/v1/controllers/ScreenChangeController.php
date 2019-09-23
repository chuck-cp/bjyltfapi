<?php
namespace api\modules\v1\controllers;
use api\modules\v1\models\MemberTeam;
use api\modules\v1\models\ShopScreenReplace;
use cms\modules\report\report;
use Yii;
use api\modules\v1\models\Screen;
use api\core\ApiController;
use api\modules\v1\models\SystemDevice;
use yii\base\Exception;

/**
 * 安装认证
 */
class ScreenChangeController extends ApiController
{
    public function behaviors(){
        //使用验证权限过滤器
        $behaviors = parent::behaviors();
        if(in_array($this->action->id,['existence','get','screennumber','post','activation'])){
            unset($behaviors['authenticator']);
        }
        return $behaviors;
    }

    /*
     * 屏幕更换未通过
     */
    public function actionChangeUpdate($shop_id){
        $replace_id = Yii::$app->request->get('replace_id');
        $ckre = ShopScreenReplace::checkIsInstallMember($replace_id);
        if($ckre != 'SUCCESS'){
            return $this->returnData($ckre);
        }
        $replaceData = ShopScreenReplace::find()->where(['id'=>$replace_id, 'install_member_id'=>Yii::$app->user->id, 'status'=>3])->count();
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
     * @return array
     */
    public function actionChangePostNewCheck(){
        $params = $this->params;
        $ckre = ShopScreenReplace::checkIsInstallMember($params['replace_id']);
        if($ckre != 'SUCCESS'){
            return $this->returnData($ckre);
        }
        $del = isset($params['remove_device_number']) ? $params['remove_device_number'] : [];
        $add = isset($params['install_software_number']) ? $params['install_software_number'] : [];
        $shop_id = $params['id'];
        if(!empty($del) && !empty($add)){
            $delErr = [];
            foreach ($del as $v){
                if(!Screen::find()->where(['shop_id'=>$shop_id, 'number'=>$v])->one()){
                    $delErr[] = $v;
                }
            }
            $addErr = [];
            $alreadyArr = [];
            foreach ($add as $v){
                //是否在系统表中出库
                if(!SystemDevice::getDevice($v)){
                    $addErr[] = $v;
                }
                //是否已经在screen表中
                if(Screen::find()->where(['software_number'=>$v])->count()){
                    $alreadyArr[] = $v;
                }
            }
            if(!empty($delErr) || !empty($addErr || !empty($alreadyArr))){
                return $this->returnData('DEL_DEVICE_NOT_FOUND',['del'=>$delErr, 'add'=>$addErr, 'alredy'=>$alreadyArr]);
            }
        }
        $replaceModel = new ShopScreenReplace();
        if($reslut = $replaceModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($reslut);
        }
        $trans = Yii::$app->db->beginTransaction();
        try{
            if(isset($replaceModel->install_software_number)){
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
        return $this->returnData('SUCCESS');
    }

    /**
     * @return array
     * 换屏后屏幕安装错误后的更换故障屏幕提交
     */
    public function actionChangePost(){
        $params = $this->params;
        if($params['replace_id']){
            $ckre = ShopScreenReplace::checkIsInstallMember($params['replace_id'],'replace', false);
        }else{
            $ckre = ShopScreenReplace::checkIsInstallMember($params['id'],'shop', false);
        }

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
            //$delArr[] = ShopScreenReplaceList::getSolft($v['id']);
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


    //change-screen-update 更换屏幕审核不通过修改信息页面
    public function actionChangeScreenUpdate(){
        $params = $this->params;
        $replace = ShopScreenReplace::findOne($params['replace_id']);
        if(!$replace){ return $this->returnData('ERROR'); }
        if($replace->status != 3){ return $this->returnData('ERROR'); }
        $replaceData = $replace->getAttributes(['id','shop_id','status','install_software_number','remove_device_number','problem_description','replace_screen_number']);
        //找图片
        $screenInfo = [];
        foreach (explode(',',$replaceData['install_software_number']) as $v){
            $screen = Screen::find()->where(['replace_id'=>$params['replace_id'],'software_number'=>$v])->select('id as screen_id,image')->asArray()->one();
            $screenInfo['images'][] = $screen['image'];
            $screenInfo['screen_id'][] = $screen['screen_id'];
        }
        return $this->returnData('SUCCESS',array_merge($replaceData,$screenInfo));
    }
    //change-new-update-post 更换屏幕安装审核不通过后修改图片
    public function actionChangeNewUpdatePost(){
        $params = $this->params;
        if(empty($params['screenIds'])){
            return $this->returnData('ERROR');
        }
        $trans = Yii::$app->db->beginTransaction();
        try{
            foreach ($params['screenIds'] as $k => $v){
                $screenModel = Screen::findOne($v);
                if(!$screenModel){
                    return $this->returnData('ERROR');
                }
                $screenModel->image = $params['new_images'][$k];
                $screenModel->save();
            }
            $replaceModel = ShopScreenReplace::findOne($params['replace_id']);
            $ckre = ShopScreenReplace::checkIsInstallMember($params['replace_id']);
            if($ckre != 'SUCCESS'){
                return $this->returnData($ckre);
            }
            $replaceModel->status = 2;
            if(empty($replaceModel->install_software_number)){
                $replaceModel->screen_status = 0;
            }
            $replaceModel->save();
            $trans->commit();
            return $this->returnData('SUCCESS');
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            //print_r($e->getMessage());exit;
            $trans->rollBack();
            return $this->returnData('ERROR');
        }

    }




}