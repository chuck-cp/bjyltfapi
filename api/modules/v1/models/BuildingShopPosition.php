<?php

namespace api\modules\v1\models;
use common\libs\ArrayClass;
use Exception;
use Yii;

/**
 * This is the model class for table "{{%building_shop_position}}".
 *
 * @property string $id
 * @property integer $shop_type
 * @property string $shop_id
 * @property string $position_id
 * @property string $position_number
 * @property integer $reference_number
 * @property string $floor_number
 * @property integer $screen_number
 * @property integer $screen_type
 * @property integer $monopoly
 * @property string $description
 */
class BuildingShopPosition extends \api\core\ApiActiveRecord
{
    public $position_data;
    //以下四个新加属性是为了计算本次提交的总设备数
    public $position_config_number;
    public $floor_number;
    public $position_number;
    public $mark_data;
    public $shop_position_id;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%building_shop_position}}';
    }

    /*
     * 获取当前要安装的楼层
     * */
    public function getIndexFloorNumber()
    {
        if ($this->index_floor_number) {
            return $this->index_floor_number;
        }
        return BuildingPositionConfig::getPositionById($this->position_id,'default_floor_number')['default_floor_number'] ?? 0;
    }

    /**
     * 楼宇大堂等候区海报位置表提交
     * @return bool
     */
    public function buildBillHallWaitCreate(){
        try{
            if($this->judgeArrIsEmpty($this->floor_number) || $this->judgeArrIsEmpty($this->position_config_number) || $this->judgeArrIsEmpty($this->position_number)){
                throw new \yii\db\Exception('楼层数量或者安装数量均不能为空');
            }
            $this->screen_number = $this->countScreenNumber($this->floor_number, $this->position_config_number, $this->position_number, $this->mark_data);
            if(!$this->screen_number){
                throw new \yii\db\Exception('楼层数量必须和安装数量数组元素个数一致');
            }
            $this->member_id = \Yii::$app->user->id;
            if($this->shop_type == 2){
                //公园时直接提交的要修改公园表总数量
                if(!$parkObj = BuildingShopPark::findOne($this->shop_id)){
                    throw new \yii\db\Exception('未找到id为：'.$this->shop_id.' 的公园记录');
                }

                $parkObj->poster_total_screen_number = $this->screen_number;
                $parkObj->poster_examine_status = 0;
                $parkObj->save();
            }
            if(isset($this->shop_position_id) && $this->shop_position_id > 0){
                $currentModel = self::findOne($this->shop_position_id);
                if(!$currentModel){
                    throw new \yii\db\Exception('未找到id为：'.$this->shop_position_id.' 的记录');
                }
                $currentModel->shop_type = $this->shop_type;
                $currentModel->screen_type = $this->screen_type;
                $currentModel->shop_id = $this->shop_id;
                $currentModel->position_id = $this->position_id;
                $currentModel->position_config_id = $this->position_config_id;
                $currentModel->monopoly = $this->monopoly;
                $currentModel->screen_number = $this->screen_number;
                $currentModel->save();
                return $currentModel->id;
            }else{
                $this->save();
                return $this->id;
            }
        }catch (Exception $e){
            Yii::error($e->getMessage().' at line: '.$e->getLine(),'error');
            return false;
        }
    }

    public function countScreenNumber($floor_number,$position_config_number, $position_number, $mark_data){
        if(count($floor_number) !== count($position_config_number) || count($floor_number) !== count($position_number) || count($floor_number) !== count($mark_data)){
            return 0;
        }
        $totalNumber = 0;
        $totalConfigArr = [];
        foreach ($mark_data as $k => $v){
            foreach ($v as $kk => $vv){
                if (!isset($totalConfigArr[$k])) {
                    $totalConfigArr[$k] = [];
                }
                if (!isset($totalConfigArr[$k][$vv])) {
                    $totalConfigArr[$k][$vv] = 0;
                }
                $totalConfigArr[$k][$vv] += $position_config_number[$k][$kk];

            }
        }
        foreach($totalConfigArr as $k => $v){
            foreach ($v as $kk => $vv){
                $totalNumber += $vv * $position_number[$k][$kk] * count(array_filter(explode(',',$floor_number[$k])));
            }
        }
        return $totalNumber;
    }
    /**
     * 楼宇大堂等候区以上海报位置表提交
     * @return bool
     */
    public function buildBillOverHallWaitCreate(){
        try{
            $this->save();
            return true;
        }catch (Exception $e){
            Yii::error($e->getMessage(),'error');
            return false;
        }
    }
    /**
     * 楼宇客梯内海报位置表提交
     * @return bool
     */
    public function passengerLadderCreate(){
        try{
            $this->save();
            return true;
        }catch (Exception $e){
            Yii::error($e->getMessage(),'error');
            return false;
        }
    }
    /**
     * 楼宇 地下客梯口 位置表提交
     * @return bool
     */
    public function undergroundElevatorEntrance(){
        try{
            $this->save();
            return true;
        }catch (Exception $e){
            Yii::error($e->getMessage(),'error');
            return false;
        }
    }


    /*
     * 获取申请信息的位置列表
     * @param shop_id int 场景店铺ID
     * @param shop_type int 场景店铺类型(写字楼或公园)
     * @param screen_type int 设备类型(1、LED 2、海报)
     * @param is_detail int 是否显示详细信息(设备规格和设备数量)
     * @param is_company int 是否显示公司和楼宇模块(1、显示)
     * */
    public function getPositionList($shop_id,$shop_type,$screen_type)
    {
        $is_company = (int)Yii::$app->request->get('is_company');
        $is_detail = (int)Yii::$app->request->get('is_detail');
        $field = 'yl_building_shop_position.id,position_id,position_name';
        if ($is_detail) {
            $field = 'yl_building_shop_position.id,position_id,screen_number,position_name';
        }
        $positionModel = self::find()->joinWith('config',false)->select($field)->where(['shop_id' => $shop_id, 'yl_building_shop_position.shop_type' => $shop_type, 'yl_building_shop_position.screen_type' => $screen_type])->asArray()->all();
        if (empty($positionModel)) {
            return [];
        }
        if ($is_detail) {

            $position_id = array_column($positionModel,'position_id');
//            $screenSpecData = BuildingShopPositionView::getScreenSpecByPositionId($position_id);
            foreach ($positionModel as $key => &$value) {
                $value['screen_status'] = BuildingShopScreen::checkNoActivationScreen($value['id']);
                $value['screen_spec'] = $screenSpecData[$value['position_id']] ?? [];
                $value['position_id'] = $value['id'];
                unset($value['id']);
            }
        }
        if ($is_company == 1) {
            array_unshift($positionModel,
                [
                    'position_id' => '0',
                    'position_name' => '公司信息'
                ],
                [
                    'position_id' => '0',
                    'position_name' => $shop_type == BuildingShop::SHOP_FLOOR ? '楼宇信息' : '公园信息'
                ]
            );
        }
        return $positionModel;
    }

    // 生成楼层的位置数组
    public function reduceFloorData($position_data,$position_config_mame,$position_config_number,$update_position_config_id = [])
    {
        $appendPosition = [];
        foreach (['id','reference_number','position_number','screen_spec','screen_start_at','screen_end_at'] as $value) {
            if (empty($position_data[$value])) {
                continue;
            }
            $positionName = BuildingPositionConfig::getPositionDetailKey($this->position_id,$value);
            if ($value == 'position_number') {
                if (strstr($positionName,",")) {
                    $parentValue = explode(",",$position_data[$value]);
                    foreach (explode(",",$positionName) as $pNameKey => $pNameValue) {
                        $appendPosition[] = ['key' => $value, 'value' => $parentValue[$pNameKey], 'name' => $pNameValue];
                        foreach ($position_config_mame as $pDataKey => $pDataValue) {
                            if ($pDataValue['mark'] == $pNameKey) {
                                $appendPosition[] = ['key' => 'position_config_number', 'value' => $position_config_number[$pDataKey], 'name' => $pDataValue['position_name'], 'update' => in_array($pDataValue['id'],$update_position_config_id) ? "1" : "0"];
                            }
                        }
                    }
                } else {
                    $appendPosition[] = ['key' => $value, 'value' => $position_data[$value], 'name' => $positionName];
                    foreach ($position_config_mame as $pDataKey => $pDataValue) {
                        $appendPosition[] = ['key' => 'position_config_number', 'value' => $position_config_number[$pDataKey], 'name' => $pDataValue['position_name'], 'update' => in_array($pDataValue['id'],$update_position_config_id) ? "1" : "0"];
                    }
                }
            } else {
                $appendPosition[] = ['key' => $value, 'value' => $position_data[$value], 'name' => $positionName];
            }
        }
        $appendPosition[] = ['key' => 'description', 'value' => $position_data['description'], 'name' => '备注'];
        return $appendPosition;
    }

    /*
     * 获取设备安装页面的位置列表
     * @param id int building_shop_position_view表的ID
     * @param update_position_config_id array 更改过的位置ID
     * */
    public function getPositionDetailOnInstall($id,$update_position_config_id)
    {
        $positionConfigName = BuildingPositionConfig::getPositionDataKeepSort(explode(",",$this->position_config_id),'id,position_name,mark');
        $positionViewData =  BuildingShopPositionView::getDataById($id,'screen_end_at,screen_start_at,position_number,position_config_number,reference_number,screen_spec,description');
        return $this->reduceFloorData($positionViewData,$positionConfigName,$positionViewData['position_config_number'],$update_position_config_id);
    }

    /*
     * 获取申请信息的位置列表
     * */
    public function getPositionDetail()
    {
        $positionConfigName = BuildingPositionConfig::getPositionDataKeepSort(explode(",",$this->position_config_id),'position_name,mark,id');
        $resultData = [
            'monopoly' => $this->monopoly
        ];
        $positionViewData =  BuildingShopPositionView::getDataByPositionId($this->id,'id,screen_end_at,screen_start_at,position_number,position_config_number,reference_number,screen_spec,floor_number,description');
        //p($positionViewData);exit;
        foreach ($positionViewData as $key => $value) {
            $positionConfigNumber = explode(",",$value['position_config_number']);
            $resultData['position_list'][] = [
                'floor_number' => $value['floor_number'] ?? null,
                'floor_data' => $this->reduceFloorData($value,$positionConfigName,$positionConfigNumber)
            ];
        }
        return $resultData;
    }

    /*
     * 计算本地提交的设备总数量
     * @param string floor_number 楼层编号(多个以逗号分割)
     * @param string position_number 位置数量
     * @param string position_config_mark 每个小位置对应的位置编号
     * @param string position_config_number 每个小位置对应的安装数量
     * */
    public function reduceScreenNumber($floor_number,$position_number,$position_config_mark,$position_config_number)
    {
        $floor_number = count(explode(",",$floor_number));
        $position_config_number = explode(",",$position_config_number);
        $resultNumber = 0;
        $position_number = explode(",",$position_number);
        foreach ($position_config_mark as $key => $mark) {
            $resultNumber += ($position_number[$mark['mark']] * $position_config_number[$key]) * $floor_number;
        }
        return $resultNumber;
    }

    /*
     * 更新申请信息的位置数据
     * */
    public function updatePosition()
    {
        $positionConfigId = explode(",",$this->position_config_id);
        $position_config_mark = BuildingPositionConfig::getPositionDataKeepSort($positionConfigId,'mark');

        $screen_number = 0;  // 本次安装的设备总数量
        $positionData = json_decode($this->position_data,true);
        $positionViewModel = new BuildingShopPositionView();
        $spacePositionViewId = [];   // 剩余的位置ID
        $dbTrans = Yii::$app->db->beginTransaction();
        try {
            foreach ($positionData as $key => $value) {
                $postData = [];
                foreach ($value['floor_data'] as $reformValue) {
                    if (isset($postData[$reformValue['key']])) {
                        $postData[$reformValue['key']] .= ','.$reformValue['value'];
                    } else {
                        $postData[$reformValue['key']] = $reformValue['value'];
                    }
                }
                $floor_number = $value['floor_number'] ?? '';
                $screen_number += $this->reduceScreenNumber($floor_number,$postData['position_number'],$position_config_mark,$postData['position_config_number']);
                if (!isset($postData['id']) || empty($postData['id'])) {
                    $cloneModel = clone $positionViewModel;
                    $cloneModel->shop_position_id = $this->id;
                    $cloneModel->position_number = $postData['position_number'];
                    $cloneModel->position_config_number = $postData['position_config_number'];
                    $cloneModel->reference_number = $postData['reference_number'] ?? 0;
                    $cloneModel->floor_number = $floor_number;
                    $cloneModel->screen_spec = $postData['screen_spec'];
                    $cloneModel->description = $postData['description'];
                    $cloneModel->screen_start_at = $postData['screen_start_at'] ?? '';
                    $cloneModel->screen_end_at = $postData['screen_start_at'] ?? '';
                    $cloneModel->save();
                    $spacePositionViewId[] = $cloneModel['id'];
                } else {
                    $positionViewModel::updateAll([
                        'position_number' => $postData['position_number'],
                        'position_config_number' => $postData['position_config_number'],
                        'reference_number' => $postData['reference_number'] ?? 0,
                        'floor_number' => $floor_number,
                        'screen_spec' => $postData['screen_spec'],
                        'description' => $postData['description'],
                        'screen_start_at' => $postData['screen_start_at'] ?? "",
                        'screen_end_at' => $postData['screen_end_at'] ?? "",
                    ],['id' => $postData['id']]);
                    $spacePositionViewId[] = $postData['id'];
                }
            }
            $reduceScreenNumber = self::find()->where(['and',['shop_id' => $this->shop_id], ['shop_type' => $this->shop_type],['screen_type' => $this->screen_type],['!=','id',$this->id]])->select('sum(screen_number) as screen_number')->asArray()->one();
            if (!$reduceScreenNumber) {
                $reduceScreenNumber['screen_number'] = 0;
            }
            $reduceScreenNumber['screen_number'] += $screen_number;
//            $shopModel = (new BuildingShop($this->shop_type,['id' => $this->shop_id],'object'))->getShopModel();
//            if ($this->screen_type == BuildingShop::SCREEN_LED) {
//                $shopModel::updateAll(['led_examine_status' => 0, 'led_screen_number' => $reduceScreenNumber['screen_number']],['id' => $this->shop_id]);
//            } else {
//                $shopModel::updateAll(['poster_examine_status' => 0, 'poster_screen_number' => $reduceScreenNumber['screen_number']],['id' => $this->shop_id]);
//            }
            if ($spacePositionViewId) {
                $positionViewModel::deleteAll(['and',['shop_position_id' => $this->id],['not in','id',$spacePositionViewId]]);
            } else {
                $positionViewModel::deleteAll(['and',['shop_position_id' => $this->id]]);
            }
            $this->screen_number = $screen_number;
            $this->save();
            $dbTrans->commit();
            return true;
        } catch (\Throwable $e) {
            $dbTrans->rollBack();
            Yii::error($e->getMessage() .' '. $e->getLine());
            return false;
        }
    }


    /*
     * 验证是否有安装权限
     * */
    public function isAuth()
    {
        $shopModel = new BuildingShop($this->shop_type,['id' => $this->shop_id]);
        return $shopModel->isAuth();
    }

    /*
     * 公园详情页获取一个位置ID
     * */
    public static function getPositionId($shop_id, $shop_type, $screen_type)
    {
        $positionModel = BuildingShopPosition::find()->where(['shop_id' => $shop_id,'shop_type' => $shop_type, 'screen_type' => $screen_type])->select('id')->asArray()->one();
        if ($positionModel) {
            return $positionModel['id'];
        }
    }

    public function getConfig()
    {
        return $this->hasOne(BuildingPositionConfig::className(),['id' => 'position_id']);
    }

    /**
     * 根据组合的唯一索引找到某楼宇或公园的某场景的安装数量和设备规格
     * @param $screen_type
     * @param $positon_id
     * @param $shop_type
     * @param $shop_id
     * @return array|bool
     */
    public static function getPositionNumberByCondition($screen_type, $positon_id, $shop_type, $shop_id){
        $positionInfo = self::find()->where(['screen_type'=>$screen_type, 'position_id'=>$positon_id, 'shop_type'=>$shop_type, 'shop_id'=>$shop_id])->select('id,screen_number')->asArray()->one();
        if(empty($positionInfo)){ return false; }
        $spec = BuildingShopPositionView::find()->where(['shop_position_id'=>$positionInfo['id']])->select('screen_spec')->asArray()->one();
        return [
            'screen_number' => $positionInfo['screen_number'],
            'spec' => $spec['screen_spec'],
        ];
    }

    /**
     * 获取已提交过得场景数据
     * @return array|bool
     */
    public function getAlredyInfo(){
        $position = self::find()->where(['shop_id'=>$this->shop_id, 'shop_type'=>$this->shop_type, 'screen_type'=>$this->screen_type, 'position_id'=>$this->position_id])->asArray()->one();
        if(empty($position)){ return false; }
        $positionView = BuildingShopPositionView::find()->where(['shop_position_id'=>$position['id']])->orderBy('id DESC')->asArray()->all();
        return [
            'positon' => $position,
            'positionView' => $positionView,
        ];
    }
    public function scenes(){
        return [
            'install-update-screen' => [
                'index_floor_number' => [
                ],
            ],
            'update'=>[
                'monopoly' => [
                    'required' => '1',
                    'result' => 'MONOPOLY_EMPTY'
                ],
                'position_data' => [
                    'required' => '1',
                    'result' => 'POSITION_DATA_EMPTY'
                ],
            ],
            //各种安装场景
            'build-scene' => [
                'shop_position_id' => [],//就是本表主键id
                'shop_type' => [
                    'required' => 1,
                    'result' => 'BUILD__EMPTY',
                ],
                'screen_type' => [
                    'required' => 1,
                    'result' => 'BUILD_SHOP_TYPE_EMPTY',
                ],
                'shop_id' => [
                    'required' => 1,
                    'result' => 'BUILD_SHOP_ID_EMPTY',
                ],
                'position_id' => [
                    'required' => 1,
                    'result' => 'BUILD_POSITION_ID_EMPTY',
                ],
                'position_config_id' => [
                    'required' => 1,
                    'result' => 'BUILD_POSITION_CONFIG_ID_EMPTY',
                ],
                'position_number' => [
                    'required' => 1,
                    'result' => 'BUILD_POSITION_NUMBER_EMPTY'
                ],

                'mark_data' => [
                    'required' => 1,
                    'result' => 'BUILD_MARK_DATA_EMPTY',
                ],

                'monopoly' => [
                    'required' => 1,
                    'result' => 'BUILD_MONOPOLY_EMPTY',
                ],
                //外加计算本次提交总数量的属性
                'position_config_number' => [
                    'required' => 1,
                    'result' => 'BUILD_POSITION_CONFIG_NUMBER_EMPTY'
                ],
                'floor_number' => [
                    'required' => 1,
                    'result' => 'BUILD_FLOOR_NUMBER_EMPTY'
                ],
            ],
            //详情页时获得已安装的信息
            'get-detail-scene-by-config-id' => [
                'shop_id' => [
                    'required' => 1,
                    'reuslt' => 'FLOOR_ID_EMPTY',
                ],
                'shop_type' => [
                    'required' => 1,
                    'reuslt' => 'SHOP_TYPE_EMPTY',
                ],
                'position_id' => [
                    'required' => 1,
                    'reuslt' => 'POSITION_ID_EMPTY',
                ],
                'screen_type' => [
                    'required' => 1,
                    'reuslt' => 'SCREEN_TYPE_EMPTY',
                ],
            ],
        ];
    }
}
