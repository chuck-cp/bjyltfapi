<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/18
 * Time: 16:06
 * 总店控制器
 */

namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\modules\v1\models\ShopHeadquarters;
use api\modules\v1\models\ShopHeadquartersList;
use yii\base\Exception;

class ShopHeadController extends ApiController{

    //总店分店信息创建
    public function actionShopHeadCreate(){
        $shopHeadModel = new ShopHeadquarters();
        if($result = $shopHeadModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $shopHeadListModel = new ShopHeadquartersList();
        if($result = $shopHeadListModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $dbTrans = \Yii::$app->db->beginTransaction();
        try{
            $result = $shopHeadModel->shopHeadCreate();
            if($result != 'SUCCESS'){
                $dbTrans->rollBack();
                return $this->returnData($result);
            }
            $shopHeadListModel->headquarters_id = $shopHeadModel->id;
            if(!$shopHeadListModel->shopListSave()){
                throw new Exception('创建分店列表失败');
            }
            $dbTrans->commit();
            return $this->returnData('SUCCESS',['shop_id'=>$shopHeadModel->id]);
        }catch (Exception $e){
            \Yii::error($e->getMessage(),'db');
            $dbTrans->rollBack();
            return $this->returnData('ERROR');
        }
    }

    //总店分店信息修改
    public function actionShopHeadModify(){
        $shopHeadModel = new ShopHeadquarters();
        if($result = $shopHeadModel->loadParams($this->params,'shop-head-create')){
            return $this->returnData($result);
        }
        $shopHeadListModel = new ShopHeadquartersList();
        if($result = $shopHeadListModel->loadParams($this->params,'shop-head-create')){
            return $this->returnData($result);
        }
        $dbTrans = \Yii::$app->db->beginTransaction();
        try{
            if(!$shopHeadModel->shopHeadModify()){
                throw new Exception('修改店铺总表信息失败');
            }

            $shopHeadListModel->headquarters_id = $shopHeadModel->id;
            if(!$shopHeadListModel->shopListModify()){
                throw new Exception('修改分店列表失败');
            }
            $dbTrans->commit();
            return $this->returnData('SUCCESS',['shop_id'=>$shopHeadModel->id]);
        }catch (Exception $e){
            \Yii::error($e->getMessage(),'db');
            $dbTrans->rollBack();
            return $this->returnData('ERROR');
        }
    }
    //获取所有总店列表
    public function actionShopHeadList(){
        $shopHeadModel = new ShopHeadquarters();
        if($result = $shopHeadModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS',$shopHeadModel->getHeadquartersList());
    }

    //获取所有分店列表
    public function actionShopBranchList(){
        $shopModel = new ShopHeadquartersList();
        if($result = $shopModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS',$shopModel->getBranchShopList());
    }

    //分店获取总店信息
    public function actionGetSubbranchInfo($head_id, $branch_id){
        $obj = ShopHeadquarters::findOne($head_id);
        $branch_obj = ShopHeadquartersList::findOne($branch_id);
        $price = ShopHeadquartersList::getBrokerageById($branch_id);
        if(!$obj || !$branch_obj){ return $this->returnData('ERROR'); }
        return $this->returnData('SUCCESS',array_merge($obj->attributes,$branch_obj->attributes,$price));
    }

    //获取总店和对应分店信息
    public function actionGetHeadBranches(){
        $head_id = \Yii::$app->request->get('head_id');
        $obj = ShopHeadquarters::findOne($head_id);
        $branch_obj = ShopHeadquartersList::find()->where(['headquarters_id'=>$head_id,'shop_id'=>0])->asArray()->all();
        if(!$obj || !$branch_obj){ return $this->returnData('ERROR'); }
        $info = [
            'head' => $obj->attributes,
            'branch' => $branch_obj,
        ];
        return $this->returnData('SUCCESS', $info);
    }
}