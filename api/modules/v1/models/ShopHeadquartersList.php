<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use api\modules\v1\models\SystemAddress;
use api\modules\v1\models\SystemAddressLevel;
use api\modules\v1\models\SystemConfig;
use common\libs\ToolsClass;
use Yii;
use yii\base\Exception;
use yii\data\Pagination;
use api\modules\v1\models\Shop;
class ShopHeadquartersList extends ApiActiveRecord
{
    public $branchName;
    public $branchArea;
    public $branchAddress;
    public $keyword;
    public $branchIds;
    public $delIds;
    public $page;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop_headquarters_list}}';
    }

    /*
     * 获取分店列表
     * */
    public function getBranchShopList(){
        $where = ['headquarters_id'=>$this->headquarters_id,'shop_id'=>0];
        if($this->keyword){
            $where = ['and',$where,['or',['like','branch_shop_name',$this->keyword],['like','branch_shop_area_name',$this->keyword],['like','branch_shop_address',$this->keyword]]];
        }
        $resultShop = self::find()->where($where)->select('id,branch_shop_area_name,branch_shop_address,branch_shop_name')->asArray()->all();
        return $resultShop;
    }
    /*
     * 我的店铺处获取分店列表带分页
     */
    public function getMyBranches(){
        $where = ['headquarters_id'=>$this->headquarters_id];
        $branch = self::find()->where($where);
        $total = $branch->count();
        $pagination = new Pagination(['totalCount'=>$total,'pageSize'=>10]);
        $pagination->validatePage = false;
        $headList = $branch->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        if(!empty($headList)){
            foreach ($headList as $k => $v){
                $branch = Shop::find()->where(['and',['headquarters_list_id'=>$v['id']],['>','status',1]])->select('shop_image,status')->asArray()->one();
                if($branch){
                    $headList[$k]['status'] = $branch['status'];
                    $headList[$k]['shop_image'] = $branch['shop_image'];
                }else{
                    $headList[$k]['status'] = '0';
                    $headList[$k]['shop_image'] = '';
                }
            }
        }
        return ['total'=>$total, 'list'=>$headList];
    }
    public function shopListSave()
    {
        if(!is_array($this->branchName)){
            $bname = $this->branchName;
            $bareaid = $this->branchArea;
            $address = $this->branchAddress;
            $this->branchName = $this->branchArea = $this->branchAddress = [];
            $this->branchName[] = $bname;
            $this->branchArea[] = $bareaid;
            $this->branchAddress[] = $address;
        }
        $num = count($this->branchName);
        if($num == 0 || count($this->branchArea) !== $num || count($this->branchAddress) !== $num){
            throw new Exception('分店数据填写不全');
        }
        //组装数据
        $attributes = [];
        for ($i=0; $i<$num; $i++){
            $attributes[$i]['headquarters_id'] = $this->headquarters_id;
            $attributes[$i]['branch_shop_name'] = $this->branchName[$i];
            $attributes[$i]['branch_shop_area_id'] = $this->branchArea[$i];
            $area_name = SystemAddress::getAreaNameById($this->branchArea[$i]);
            $attributes[$i]['branch_shop_area_name'] = $area_name ==true ? $area_name : '暂无地区';
            $attributes[$i]['branch_shop_address'] = $this->branchAddress[$i];

        }
        //print_r($attributes);exit;
        try{
            if(empty($attributes)){
                throw new Exception('分店数据填有误');
            }
            $models=new self();
            foreach ($attributes as $k => $v){
                $model = clone $models;
                //$model->setAttributes($v);
                $model->headquarters_id = $v['headquarters_id'];
                $model->branch_shop_name = $v['branch_shop_name'];
                $model->branch_shop_area_id = $v['branch_shop_area_id'];
                $model->branch_shop_address = $v['branch_shop_address'];
                $model->branch_shop_area_name = $v['branch_shop_area_name'];
                $model->save();
            }
            return true;
        }catch (\yii\base\Exception $e){
            \Yii::error($e->getMessage(),'db');
            throw new Exception('[ERROR]创建分店失败');
            return false;
        }
    }
    //修改或添加分店信息
    public function shopListModify(){
        $num = count($this->branchName);
        if($num == 0 || count($this->branchArea) !== $num || count($this->branchAddress) !== $num){
            throw new Exception('分店数据填写不全');
        }
        //组装数据
        $attributes = [];
        for ($i=0; $i<$num; $i++){
            $attributes[$i]['headquarters_id'] = $this->headquarters_id;
            $attributes[$i]['branch_shop_name'] = $this->branchName[$i];
            $attributes[$i]['branch_shop_area_id'] = $this->branchArea[$i];
            $area_name = SystemAddress::getAreaNameById($this->branchArea[$i]);
            $attributes[$i]['branch_shop_area_name'] = $area_name ==true ? $area_name : '暂无地区';
            $attributes[$i]['branch_shop_address'] = $this->branchAddress[$i];
            if($this->branchIds && isset($this->branchIds[$i])){
                $attributes[$i]['id'] = $this->branchIds[$i];
            }

        }
        //print_r($attributes);
        //print_r($this->delIds);exit;
        try{
            if(empty($attributes)){
                throw new Exception('分店数据填有误');
            }
            $models=new self();
            if(!empty($this->delIds)){
                foreach ($this->delIds as $v){
                    self::findOne($v)->delete();
                }
            }
            foreach ($attributes as $k => $v){
                if(isset($v['id'])){
                    $model = self::findOne($v['id']);
                }else{
                    $model = clone $models;
                }
                $model->headquarters_id = $v['headquarters_id'];
                $model->branch_shop_name = $v['branch_shop_name'];
                $model->branch_shop_area_id = $v['branch_shop_area_id'];
                $model->branch_shop_address = $v['branch_shop_address'];
                $model->branch_shop_area_name = $v['branch_shop_area_name'];
                $model->save();
            }
            return true;
        }catch (\yii\base\Exception $e){
            \Yii::error($e->getMessage(),'db');
            var_dump($e->getMessage());exit;
            throw new Exception('[ERROR]创建分店失败');
            return false;
        }
    }
    //分店获取每月佣金和首年佣金
    public static function getBrokerageById($branch_id){
        $branch = self::findOne($branch_id);
        if(!$branch){
            throw new \yii\base\Exception('[ERROR]分店ID错误');
        }
        $level = SystemAddressLevel::getLevel($branch->branch_shop_area_id);
        $yearPrice = SystemConfig::getConfig('system_price_first_install_'.$level);
        $monthPrice = SystemConfig::getConfig('system_price_subsidy_'.$level);
        return [
            'by_year'=> $yearPrice,
            'by_month' => $monthPrice,
            'price_token' => ToolsClass::getKeeperBrokerageToken($yearPrice,$monthPrice)
        ];

    }

    public function checkHeadId(){
        if($this->isNewRecord){
            $number = self::find()->where(['headquarters_id'=>$this->headquarters_id])->count();
            if($number > 250){
                return 'BRANCH_NUMBERS_TOO_MANY';
            }
            if(empty($this->headquarters_id)){
                return false;
            }
        }
        return true;
    }
    public function scenes(){
        return [
            'shop-branch-list'=>[
                'headquarters_id'=>[
                    'required'=>'1',
                    'result'=>'HEADQUARTERS_ID_EMPTY'
                ],
                'keyword'=>[

                ]
            ],
            'get-branch'=>[
                'headquarters_id'=>[
                    'required'=>'1',
                    'result'=>'HEADQUARTERS_ID_EMPTY'
                ],
                'page'=>[]
            ],
            'add-branch'=>[
                'headquarters_id'=>[
                    'required'=>'1',
                    'result'=>'HEADQUARTERS_ID_EMPTY'
                ],
                'branch_shop_name'=>[
                    'required'=>'1',
                    'result'=>'BRANCH_SHOP_NAME_EMPTY'
                ],
                'branch_shop_area_id'=>[
                    'required'=>'1',
                    'result'=>'BRANCH_SHOP_AREA_ID_EMPTY'
                ],
                'branch_shop_address'=>[
                    'required'=>'1',
                    'result'=>'BRANCH_SHOP_ADDRESS_EMPTY'
                ],
            ],
            'shop-head-create' => [
                'id' => [],
                'headquarters_id' =>[],
                'branchName' =>[
                    'required'=>'1',
                    'result'=>'branchName_EMPTY'
                ],
                'branchArea' =>[
                    'required'=>'1',
                    'result'=>'branchArea_EMPTY'
                ],
                'branchAddress' =>[
                    'required'=>'1',
                    'result'=>'branchAddress_EMPTY'
                ],
                'branchIds' => [],
                'delIds' => [],
            ],
            'add-branch' => [
                'id' => [],
                'headquarters_id' =>[
                    'function'=>'this::checkHeadId',
                    'result'=>'HEAD_ID_ERROR'
                ],

                'branchName' =>[
                    'required'=>'1',
                    'result'=>'branchName_EMPTY'
                ],
                'branchArea' =>[
                    'required'=>'1',
                    'result'=>'branchArea_EMPTY'
                ],
                'branchAddress' =>[
                    'required'=>'1',
                    'result'=>'branchAddress_EMPTY'
                ],
                'branchIds' => [],
                'delIds' => [],
            ],
        ];
    }

    public function getShop()
    {
        return $this->hasOne(Shop::className(),['id' => 'shop_id'])->select('status');
    }
}
