<?php

namespace api\modules\v1\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\libs\ToolsClass;
use api\modules\v1\models\SystemAddressLevel;
/**
 * 系统配置
 */
class SystemConfig extends \api\core\ApiActiveRecord
{
    const INSTALL_PRICE = 100000;
    public $equiment_type;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%system_config}}';
    }

    public static function getAllConfigById($id){
        $configModel = self::find()->where(['id'=>$id])->select('id,content')->asArray()->all();
        if(empty($configModel)){
            return [];
        }
        $configModel = ArrayHelper::map($configModel,'id','content');
        return $configModel;
    }

    public function getConfigById($id){
        return self::find()->where(['id'=>$id])->select('content')->asArray()->one();
    }

    public static function getConfig($id,$defaultValue = ''){
        $config = self::find()->where(['id'=>$id])->select('content')->asArray()->one();
        if($config){
            return $config['content'];
        } else {
            return $defaultValue;
        }
    }
    /*
     * 获取某地区下的屏幕安装费用 system_price_install_1
     */
    public static function getAreaInstallPrice($area, $money_type)
    {
        if(!$area){
            return false;
        }
        $area = substr($area,0,9);
        $level = SystemAddressLevel::find()->where(['area_id'=>$area])->one();
        if(!is_object($level)){
            return self::findOne($money_type.'3')->content;
        }
        if(!in_array($level->level,[1,2,3])){
            return self::findOne($money_type.'3')->content;
        }
        return self::findOne($money_type.$level->level)->content;

    }
    /*
     * 根据地区获得地区等级
     */
    public static function getLevelByArea($area)
    {
        if(!is_int($area)){
            return false;
        }
    }

    public function getLedOrPosterSpec(){
        if($this->equiment_type == 'led'){
            $id = 'led_spec';
        }elseif ($this->equiment_type == 'poster'){
            $id = 'frame_device_size';
        }else{
            return false;
        }
        $re = self::getConfig($id,[]);
        return $re ? array_filter(explode(',', $re)) : false;
    }
    /*
   * 场景
   * */
    public function scenes()
    {
        return [
            'get-led-or-poster-spec'=>[
                'equiment_type'=>[
                    'required'=>'1',
                    'result' => 'EQUIMENT_TYPE_TMPTY',
                ],
            ],
        ];
    }
}
