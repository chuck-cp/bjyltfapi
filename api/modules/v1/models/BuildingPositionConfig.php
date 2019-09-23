<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "{{%building_position_config}}".
 *
 * @property string $id
 * @property string $parent_id
 * @property integer $shop_type
 * @property integer $led
 * @property integer $poster
 * @property string $position_name
 */
class BuildingPositionConfig extends \api\core\ApiActiveRecord
{
    public $build_id;
    public static $positionName;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%building_position_config}}';
    }

    /**
     *获取楼宇海报安装场景
     */
    public function getBuildBillScenes(){
        //获取楼宇类型 shop_type 1、写字楼 2、商住两用
        $shop_type = BuildingShopFloor::find()->where(['id'=>$this->build_id])->select('floor_type')->asArray()->one();
        if(empty($shop_type)){
            return 'BUILD_NOT_EXIST';
        }
        $floor_type = $shop_type['floor_type'] == 1 ? 1 : 3;
        //$floor_type = 1;
        $res = self::find()->where(['shop_type'=>$floor_type, 'screen_type'=>$this->screen_type, 'parent_id'=>0])->select('id, position_name')->asArray()->all();
        if(!empty($res)){
            foreach ($res as $k => $v){
                $deviceInfo = BuildingShopPosition::getPositionNumberByCondition($this->screen_type, $v['id'], 1, $this->build_id);
                if($deviceInfo){
                    $res[$k]['screen_number'] = $deviceInfo['screen_number'];
                    $res[$k]['spec'] = $deviceInfo['spec'];
                }
            }
        }
        //p($res);
        //exit;
        return $res;
    }

    /**
     * 根据id获取下级表单内容
     * @return array
     */
    public function getInputs(){
        //1.description
        $description = self::findOne($this->id)->getAttribute('description');
        $desc = json_decode($description,true);
        //下级表单内容
        $inputs = self::find()->where(['parent_id'=>$this->id])->select(['id','position_name','description','mark'])->asArray()->all();
        if(empty($inputs)){
            return [];
        }
        $recombination = [];
        if(isset($desc['position_number'])){
            if(!strpos($desc['position_number'], ',')){
                $recombination[0]['id'] = 0;
                $recombination[0]['key'] = 'position_number';
                $recombination[0]['value'] = $desc['position_number'];
                $recombination[0]['placeholder'] = str_replace('数量','',$desc['position_number']);
            }else{
                $explodes = explode(',',$desc['position_number']);
                foreach($explodes as $k => $v){
                    $special[$k]['id'] = 0;
                    $special[$k]['key'] = 'position_number';
                    $special[$k]['value'] = $v;
                    $special[$k]['placeholder'] = str_replace('数量','',$v);
                }
            }
        }
        if(isset($desc['reference_number'])){
            if(!empty($recombination)){
                $recombination[1]['id'] = 0;
                $recombination[1]['key'] = 'reference_number';
                $recombination[1]['value'] = $desc['reference_number'];
                $recombination[1]['placeholder'] = str_replace('数量','',$desc['reference_number']);
            }else{
                $recombination[0]['id'] = 0;
                $recombination[0]['key'] = 'reference_number';
                $recombination[0]['value'] = $desc['reference_number'];
                $recombination[0]['placeholder'] = str_replace('数量','',$desc['reference_number']);
            }
        }
        $reform = [];
        foreach ($inputs as $k => $v){
            $reform[$k]['id'] = $v['id'];
            $reform[$k]['key'] = 'position_name';
            $reform[$k]['mark'] = $v['mark'];
            $reform[$k]['value'] = $v['position_name'];
            $reform[$k]['placeholder'] = str_replace('数量','',json_decode($v['description'],true)['placeholder']);
        }
        if(isset($special)){
            foreach ($special as $k => $v){
                $newArr[] = $special[$k];
                foreach ($reform as $kk => $vv){
                    if($vv['mark'] == $k){
                        $newArr[] = $vv;
                    }
                }
            }
            if(isset($recombination[0])){
                array_unshift($newArr, $recombination[0]);
            }
        }else{
            $newArr = array_merge($recombination, $reform);
        }
        return $newArr;
    }

    /**
     *获得某场景下已填写过得设备申请信息
     */
    public function getAlredyWrite(){
        $already = (new BuildingShopPosition())->getAlredyInfo();
        var_dump($already);
        if(!$already){ return false; }
        $position = $already['positon'];
        $positionView = $already['positionView'];
    }
    /**
     *获取楼宇LED安装场景
     */
    public static function getBuildLedScenes(){
        return self::find()->where(['shop_type'=>1, 'screen_type'=>2])->select('id, position_name')->asArray()->all();
    }
    /**
     *获取楼宇LED安装场景
     */
    public static function getParkBillScenes(){
        return self::find()->where(['shop_type'=>2, 'screen_type'=>2])->select('id, position_name')->asArray()->all();
    }

    /*
     * 根据ID获取位置数据
     * */
    public static function getPositionById($position_id,$filed = '*') {
        if (is_array($position_id)) {
            return self::find()->where(['id' => $position_id])->select($filed)->asArray()->all();
        } else {
            return self::find()->where(['id' => $position_id])->select($filed)->asArray()->one();
        }
    }

    /*
     * 获取在不同的位置中某个key对应的汉字说明
     * */
    public static function getPositionDetailKey($position_id,$filed)
    {
        if ($filed == 'floor_number') {
            return '安装层数';
        } elseif ($filed == 'screen_spec') {
            return '设备类型';
        } elseif ($filed == 'screen_end_at') {
            return '关机时间';
        } elseif ($filed == 'screen_start_at') {
            return '开机时间';
        } elseif ($filed == 'id') {
            return '';
        }

        $configModel = BuildingPositionConfig::find()->where(['id' => $position_id])->select('description')->asArray()->one();
        if (empty($configModel)) {
            return null;
        }
        $description = json_decode($configModel['description'],true);
        if (isset($description[$filed])) {
            return $description[$filed];
        }
        return null;
    }

    /*
     * 获取位置名称
     * */
    public static function getPositionNameById($position_id)
    {
        if (!isset(self::$positionName[$position_id])) {
            $positionModel = self::find()->where(['id' => $position_id])->select('position_name')->asArray()->one();
            if (empty($positionModel)) {
                return;
            }
            self::$positionName[$position_id] = $positionModel['position_name'];
        }
        return self::$positionName[$position_id];
    }

    /*
     * 获取位置数据并保持原ID排序
     * */
    public static function getPositionDataKeepSort($position_id,$filed)
    {
        $result = [];
        foreach ($position_id as $id) {
            $pData = self::find()->where(['id' => $id])->select($filed)->asArray()->one();
            if (!empty($pData)) {
                $result[] = $pData;
            }
        }
        return $result;
    }

    public function scenes(){
        return [
            'build-bill-scenes' => [
                'build_id' => [
                    'required' => 1,
                    'reuslt' => 'BUILD_ID_EMPTY',
                ],
                'screen_type' => [
                    'required' => 1,
                    'reuslt' => 'SCREEN_TYPE_EMPTY',
                ],
            ],
            'get-detail-scene-by-config-id' => [
                'id' => [
                    'required' => 1,
                    'reuslt' => 'CONFIG_ID_EMPTY',
                ],
            ],
        ];
    }

}
