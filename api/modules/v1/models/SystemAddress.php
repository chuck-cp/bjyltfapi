<?php

namespace api\modules\v1\models;

use common\libs\Redis;
use common\libs\RedisClass;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * 系统地区
 */
class SystemAddress extends \api\core\ApiActiveRecord
{
    public $area_id;
    public $parent_id;
    public $only;
    public $hkout;
    public static function tableName()
    {
        return '{{%system_address}}';
    }

    /*
     * 获取地区
     * */
    public function getArea(){
        if($this->is_buy == 1){
            $where['is_buy'] = 1;
            if ($this->parent_id == 0) {
                #$where['level'] = 4;
                # 如果是获取可以购买的地区，暂时只返回北上广深
                $where = ['in','id',[1011101,1013101,1014401,1014403,1011201,1013301]];
            } else {
                $where['parent_id'] = $this->parent_id;
            }
        } else {
            if($this->parent_id == 0){
                $where['parent_id'] = 101;
            }else{
                $where['parent_id'] = $this->parent_id;
            }
        }
        if($this->only != 0){
            $where = ['in','id',[1011101,1013101,1014401,1014403,1012301,1011201]];
            $areaArr = self::find()->where($where)->select('id,name')->asArray()->all();
            //array_unshift($areaArr,['id' => '101', 'name'=>'全国']);
            return $areaArr;
        }
        $data = self::find()->where($where)->select('id,name')->asArray()->all();
        if($this->hkout == 1){
            foreach ($data as $k => $v){
                if($v['id'] == 10166){
                    unset($data[$k]);
                }
            }
        }
        return $data;
    }
    /*
         * 获取地区
         * */
    public function getAreaOnly(){
        $where = [];
        if($this->only != 0){
            $where = ['in','id',[1011101,1013101,1014401,1014403]];
        }
        if(empty($where)){
            return [];
        }
        $areaArr = self::find()->where($where)->select('id,name')->asArray()->all();
        //array_unshift($areaArr,['id' => '101', 'name'=>'全国']);
        return $areaArr;
    }
    /*
     * 重组地区缓存
     * */
    public function reformAreaCache(){
        if($this->area_id){
            if(strstr($this->area_id,',')){
                $this->area_id = explode(',',$this->area_id);
            }else{
                $this->area_id = [$this->area_id];
            }
        }
        return true;
    }

    /*
     * 获取地区名称
     * */
    public static function getName($id){
        if(!$areaName = Redis::getInstance(2)->hget("system_address",$id)){
            if($systemAddress = SystemAddress::find()->where(['id'=>$id])->select('name')->asArray()->one()){
                $areaName = $systemAddress['name'];
                Redis::getInstance(2)->hset("system_address",$id,$areaName);
            }
        }
        return $areaName;
    }

    /*
     * 获取当前地区和上级的地区名称
     * */
    public static function getAreaAndParentName($area_id){
        $area_type = SystemAddress::reduceAreaType($area_id);
        if($area_type == 1){
            //省
            return SystemAddress::getName($area_id);
        }else{
            $parent_id = $area_type == 4 ? substr($area_id,0,9) : substr($area_id,0,strlen($area_id) - 2);
            return SystemAddress::getName($parent_id).' '.SystemAddress::getName($area_id);
        }
    }
    /*
     * 计算地区类型
     * */
    public static function reduceAreaType($area_id){
        $area_id_length = strlen($area_id);
        if($area_id_length == 5){
            return 1;
        }elseif($area_id_length == 7){
            return 2;
        }elseif($area_id_length == 9){
            return 3;
        }elseif($area_id_length == 12){
            return 4;
        }
        return 0;
    }

    /*
     * 根据地区ID获取名称
     * */
    public static function getAreaNameById($area_id,$result='ALL'){
        $startLen = 5;
        $resultArea = '';
        if($result == 'ALL'){
            $areaLen = strlen($area_id);
            while(True){
                if($startLen > $areaLen){
                    break;
                }
                $sAreaId = substr($area_id,0,$startLen);
                $resultArea .= SystemAddress::getName($sAreaId);
                if($startLen == 9){
                    $startLen += 3;
                }else{
                    $startLen += 2;
                }
            }
        }else{
            $resultArea .= SystemAddress::getName($area_id);
        }
        return $resultArea;
    }
    /*
     * 根据地区id获取上级地区名称
     */
    public static function getPrevAreaByIdLen($id){
        if(!$id) return '';
        $len = strlen($id);
        switch ($len){
            case 5:
                $prev_id = substr($id, 0, 3);
                $pid = false;
                break;
            case 7:
                $prev_id = substr($id, 0, 5);
                $pid = false;
                break;
            case 9:
                $prev_id = substr($id, 0, 7);
                $pid = substr($id, 0, 5);
                break;
            case 12:
                $prev_id = substr($id, 0, 9);
                $pid = substr($id, 0, 7);
                break;
            default:
                $prev_id = $id;
                $pid = false;
        }
        return $pid == true ? self::getAreaNameById($pid, 'ONE').' '.self::getAreaNameById($prev_id, 'ONE') : self::getAreaNameById($prev_id, 'ONE');
    }
    /*
     * 获取以支持购买广告的地区
     * */
    public static function getAreaByOrder($parent_id,$insert_at=null){
        $where['parent_id'] = $parent_id;
        $where['is_buy'] = 1;
        if($insert_at){
            $where = ['and',$where,['<=','install_at',$insert_at]];
        }
        return SystemAddress::find()->where($where)->select('id,name')->asArray()->all();
    }

    /*
     * 根据地区缓存获取地区列表
     * */
    public static function getAreaByCache($area_cache,$parent_id,$show_self=0){
        $parent_id_len = strlen($parent_id);
        if(empty($parent_id)){
            if($show_self == 1){
                foreach($area_cache as $key=>$area){
                    $reformArea[$area] = $key;
                }
            }else{
                $step = 5;
                foreach($area_cache as $key=>$area){
                    $reformArea[substr($area,0,$step)] = $key;
                }
            }

        }else{
            if($parent_id_len >= 9){
                $step = strlen($parent_id) + 3;
            }else{
                $step = strlen($parent_id) + 2;
            }
            foreach($area_cache as $key=>$area){
                if($area == $parent_id){
                    continue;
                }
                if(substr($area,0,$parent_id_len) == $parent_id){
                    $reformArea[substr($area,0,$step)] = $key;
                }
            }
        }
        if(empty($reformArea)){
            return [];
        }
        $reformArea = array_flip($reformArea);
        foreach($reformArea as $area){
            if(empty($area)){
                continue;
            }
            $areaName = SystemAddress::getName($area);
            if(empty($areaName)){
                continue;
            }
            $resultArea[] = [
                'id'=>(string)$area,
                'name'=>$areaName
            ];
        }
        return $resultArea;
    }

    /*
     * 获取订单已选择的地区
     * @params areaArray array 地区ID集合
     * @params parent_id int 上级ID
     * @params buy_at date 购买日期
     * @params show_self int 显示本级(如果该值是1,则显示本级的地区是否从省级开始显示)
     * */
    public static function getOrderSelectArea($area_data,$area_type,$parent_id,$buy_at=null,$show_self=0){
        $parent_id_len = strlen($parent_id);
        if($area_type == 1 && $parent_id_len){
            return self::getAreaByOrder($parent_id,$buy_at);
        }elseif($area_type == 2 && $parent_id_len > 5){
            return self::getAreaByOrder($parent_id,$buy_at);
        }elseif($area_type == 3 && $parent_id_len > 7){
            return self::getAreaByOrder($parent_id,$buy_at);
        }
        return self::getAreaByCache($area_data,$parent_id,$show_self);
    }

    /*
     * 获取地区下的街道数量
     * */
    public static function getStreetNumber($area_id_list){
        if(strlen($area_id_list[0]) == 12){
            return count($area_id_list);
        }
        $streetNumber = 0;
        foreach($area_id_list as $area_id){
            $streetNumber += SystemAddress::find()->where(['left(parent_id,'.strlen($area_id).')'=>$area_id,'level'=>6,'is_buy'=>1])->count();
        }
        return $streetNumber;
    }

    /*
     * 获取地区下的街道
     * 入参：街道(同级) 数组
     * 返回：所有街道的数组
     */
    public static function getAllStreetsByArea($area_list){
        if(!is_array($area_list)) {
            return [];
        }
        $area_length = strlen($area_list[0]);
        $street_id = RedisClass::smembers('system_street_id',3);
        if(empty($street_id)){
            $street_id = self::find()->where(['level'=>6,'is_buy'=>1])->select('id')->asArray()->all();
            $street_id = array_column($street_id,'id');
        }
        $resultArea = [];
        foreach($street_id as $key=>$val){
           if(in_array(substr($val,0,$area_length),$area_list)){
                $resultArea[] = $val;
           }
        }
        return $resultArea;
    }

    /*
     * 场景
     * */
    public function scenes(){
        return [
            'area'=>[
                'area_id'=>[
                    'function'=>'this::reformAreaCache'
                ],
                'parent_id'=>[
                    'type'=>'int'
                ],
                'is_buy'=>[
                    'type'=>'int'
                ],
                'only' => [
                    'default' => 0,
                ],
                'hkout' => [
                    'default' => 0,
                ],
            ],
        ];
    }
}
