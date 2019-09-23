<?php

namespace api\modules\v1\models;

use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "{{%building_shop_position_view}}".
 *
 * @property string $id
 * @property string $position_id
 * @property string $position_config_id
 * @property string $screen_number
 */
class BuildingShopPositionView extends \api\core\ApiActiveRecord
{
    public $view_id;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%building_shop_position_view}}';
    }
    /**
     * 楼宇大堂等候区海报位置详情表提交
     * @return bool
     */
    public function buildBillHallWaitCreate(){
        try{
            //p($this->screen_start_at);
            if($this->judgeArrIsEmpty($this->position_number) || $this->judgeArrIsEmpty($this->position_config_number) || $this->judgeArrIsEmpty($this->screen_spec) || $this->judgeArrIsEmpty($this->floor_number)){
                throw new Exception('提交数据不一致(数组个数不对应)');
            }
            $currentModel = new self();
            foreach($this->position_number as $k => $v){
                if($this->view_id[$k] == 0){
                    $currentObj = clone $currentModel;
                }else{
                    if(!$currentObj = self::findOne($this->view_id[$k])){
                        throw new Exception(self::tableName().' 表中id为：'.$this->view_id[$k].' 的记录未找到');
                    }
                }
                $currentObj->position_number = implode($v,',');
                $currentObj->position_config_number = implode($this->position_config_number[$k],',');
                if(isset($this->reference_number)){
                    $currentObj->reference_number = implode($this->reference_number[$k],',');
                }else{
                    $currentObj->reference_number = 0;
                }
                $currentObj->screen_spec = $this->screen_spec[$k];
                $currentObj->floor_number = $this->floor_number[$k];
                if(isset($this->description)){
                    $currentObj->description = $this->description[$k];
                }else{
                    $currentObj->description = ' ';
                }
                if(isset($this->screen_start_at) && !empty($this->screen_start_at)){
                    $currentObj->screen_start_at = $this->screen_start_at[$k];
                }else{
                    $currentObj->screen_start_at = '';
                }
                if(isset($this->screen_end_at) && !empty($this->screen_end_at)){
                    $currentObj->screen_end_at = $this->screen_end_at[$k];
                }else{
                    $currentObj->screen_end_at = '';
                }
                $currentObj->shop_position_id = $this->shop_position_id;
                $currentObj->save();
            }
            return true;
        }catch (\yii\base\Exception $e){
            Yii::error($e->getMessage().' at line: '.$e->getLine(),'error');
            return false;
        }
    }
//    public function buildBillHallWaitCreate(){
//        try{
//            if($this->judgeArrIsEmpty($this->position_number) || $this->judgeArrIsEmpty($this->position_config_number) || $this->judgeArrIsEmpty($this->reference_number) || $this->judgeArrIsEmpty($this->screen_spec) || $this->judgeArrIsEmpty($this->floor_number)){
//                throw new Exception('提交数据不一致(数组个数不对应)');
//            }
//            $currentModel = new self();
//            foreach($this->position_number as $k => $v){
//                $thisModel = clone $currentModel;
//                $thisModel->shop_position_id = $this->shop_position_id;
//                $thisModel->position_number = $v;
//                $thisModel->position_config_number = $this->position_config_number[$k];
//                $thisModel->reference_number = $this->reference_number;
//                $thisModel->screen_spec = $this->screen_spec[$k];
//                $thisModel->floor_number = $this->floor_number[$k];
//                $thisModel->description = $this->description[$k];
//                $thisModel->save();
//            }
//            return true;
//        }catch (Exception $e){
//            Yii::error($e->getMessage().' at line: '.$e->getLine(),'error');
//            return false;
//        }
//    }
    public function scenes(){
        return [
            'build-scene' => [
                'view_id' => [],//shop_position_id
                'position_number' => [
                    'required' => 1,
                    'result' => 'BUILD_POSITION_NUMBER_EMPTY'
                ],
                'position_config_number' => [
                    'required' => 1,
                    'result' => 'BUILD_POSITION_CONFIG_NUMBER_EMPTY'
                ],
                'reference_number' => [
                    //'required' => 1,
                    //'result' => 'BUILD_REFERENCE_NUMBER_EMPTY'
                ],
                'screen_spec' => [
                    'required' => 1,
                    'result' => 'BUILD_SCREEN_SPEC_EMPTY'
                ],
                'floor_number' => [
                    'required' => 1,
                    'result' => 'BUILD_FLOOR_NUMBER_EMPTY'
                ],
                'description' => [],
                'screen_end_at' => [],
                'screen_start_at' => [],
            ],
            'build-bill-over-hall-wait' => [
                'shop_position_id' => [
                    'required' => 1,
                    'result' => 'BUILD_SHOP_POSITION_ID_EMPTY'
                ],
                'position_number' => [
                    'required' => 1,
                    'result' => 'BUILD_POSITION_NUMBER_EMPTY'
                ],
                'position_config_number' => [
                    'required' => 1,
                    'result' => 'BUILD_POSITION_CONFIG_NUMBER_EMPTY'
                ],
                'reference_number' => [
                    'required' => 1,
                    'result' => 'BUILD_REFERENCE_NUMBER_EMPTY'
                ],
                'screen_spec' => [
                    'required' => 1,
                    'result' => 'BUILD_SCREEN_SPEC_EMPTY'
                ],
                'floor_number' => [
                    'required' => 1,
                    'result' => 'BUILD_FLOOR_NUMBER_EMPTY'
                ],
                'description' => [],
            ],
            'passenger-ladder' => [
                'shop_position_id' => [
                    'required' => 1,
                    'result' => 'BUILD_SHOP_POSITION_ID_EMPTY'
                ],
                'position_number' => [
                    'required' => 1,
                    'result' => 'BUILD_POSITION_NUMBER_EMPTY'
                ],
                'position_config_number' => [
                    'required' => 1,
                    'result' => 'BUILD_POSITION_CONFIG_NUMBER_EMPTY'
                ],
                'reference_number' => [
                    'required' => 1,
                    'result' => 'BUILD_REFERENCE_NUMBER_EMPTY'
                ],
                'screen_spec' => [
                    'required' => 1,
                    'result' => 'BUILD_SCREEN_SPEC_EMPTY'
                ],
                'floor_number' => [
                    'required' => 1,
                    'result' => 'BUILD_FLOOR_NUMBER_EMPTY'
                ],
                'description' => [],
            ],
            'underground-elevator-entrance' => [
                'shop_position_id' => [
                    'required' => 1,
                    'result' => 'BUILD_SHOP_POSITION_ID_EMPTY'
                ],
                'position_number' => [
                    'required' => 1,
                    'result' => 'BUILD_POSITION_NUMBER_EMPTY'
                ],
                'position_config_number' => [
                    'required' => 1,
                    'result' => 'BUILD_POSITION_CONFIG_NUMBER_EMPTY'
                ],
                'reference_number' => [
                    'required' => 1,
                    'result' => 'BUILD_REFERENCE_NUMBER_EMPTY'
                ],
                'screen_spec' => [
                    'required' => 1,
                    'result' => 'BUILD_SCREEN_SPEC_EMPTY'
                ],
                'floor_number' => [
                    'required' => 1,
                    'result' => 'BUILD_FLOOR_NUMBER_EMPTY'
                ],
                'description' => [],
            ],


        ];
    }


    /*
     * 根据ID获取位置数据
     * */
    public static function getDataByPositionId($position_id,$filed = '*') {
        return self::find()->where(['shop_position_id' => $position_id])->select($filed)->asArray()->all();
    }

    /*
     * 根据ID获取位置数据
     * */
    public static function getDataById($id,$filed = '*') {
        if (is_array($id)) {
            return self::find()->where(['id' => $id])->select($filed)->asArray()->all();
        } else {
            return self::find()->where(['id' => $id])->select($filed)->asArray()->one();
        }
    }

    /*
     * 根据ID获取设备规格
     * */
    public static function getScreenSpecByPositionId($position_id) {
        $screenSpecData = self::find()->where(['shop_position_id' => $position_id])->groupBy('screen_spec')->select('shop_position_id,screen_spec')->asArray()->all();
        if (empty($screenSpecData)) {
            return [];
        }
        $result = [];
        foreach ($screenSpecData as $key => $value) {
            $result[$value['shop_position_id']][] = $value['screen_spec'];
        }
        return $result;
    }

}
