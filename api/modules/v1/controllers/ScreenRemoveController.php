<?php
namespace api\modules\v1\controllers;
use api\modules\v1\models\MemberTeam;
use api\modules\v1\models\ShopScreenReplace;
use Yii;
use api\core\ApiController;
use api\modules\v1\models\Screen;
use api\modules\v1\models\Shop;
use api\modules\v1\models\ShopApply;
use yii\base\Exception;

/**
 * 安装认证
 */
class ScreenRemoveController extends ApiController
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
        $replaceData = ShopScreenReplace::find()->where(['id'=>$replace_id, 'install_member_id'=>Yii::$app->user->id, 'status'=>3])->count();
        if(!$replaceData){
            return $this->returnData('ERROR');
        }
        //获取需要修改的屏幕信息
        $updateList = Screen::find()->where(['replace_id'=>$replace_id])->asArray()->all();
        $ckre = ShopScreenReplace::checkIsInstallMember($replace_id);
        if($ckre != 'SUCCESS'){
            return $ckre;
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
                        //change-not-pass-update
        $params = $this->params;
        $replaceObj = ShopScreenReplace::findOne($params['replace_id']);
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
            //这里只检验，并不真正的删除
            foreach ($del as $v){
                if(!Screen::find()->where(['shop_id'=>$shop_id, 'number'=>$v])->one()){
                    $delErr[] = $v;
                }
            }
            if(!empty($delErr)){
                //如果至少有一个不在店里就返回
                return $this->returnData('DEL_DEVICE_NOT_FOUND',$delErr);
            }
        }
        $shopScreenModel = new ShopScreenReplace();
        if($result = $shopScreenModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
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