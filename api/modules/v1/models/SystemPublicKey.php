<?php

namespace api\modules\v1\models;

use common\libs\ToolsClass;
use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "{{%system_public_key}}".
 *
 * @property string $device_number
 * @property string $public_key
 */
class SystemPublicKey extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%system_public_key}}';
    }

    /*
     * 验证公钥
     * */
    public static function validatePublicKey(){
        #Yii::error(json_encode($_SERVER),'db');
        $device_number = isset($_SERVER['HTTP_DEVICE_NUMBER']) ? $_SERVER['HTTP_DEVICE_NUMBER'] : '';
        $public_key = isset($_SERVER['HTTP_PUBLIC_KEY']) ? $_SERVER['HTTP_PUBLIC_KEY'] : '';

        if(empty($device_number) || empty($public_key)){
            \Yii::error("[DEVICE_NUMBER_OR_PUBLIC_KEY_EMPTY]device_number:{$device_number} public_key:{$public_key}",'db');
            return 'DEVICE_NUMBER_OR_PUBLIC_KEY_EMPTY';
        }
        #Yii::error($device_number,'db');
        $keyModel = SystemPublicKey::find()->where(['device_number'=>$device_number])->select('public_key')->asArray()->one();
        if(empty($keyModel)){
            \Yii::error("[PUBLIC_KEY_EMPTY]device_number:{$device_number} public_key:{$public_key}",'db');
            return 'PUBLIC_KEY_EMPTY';
        }
        #Yii::error(md5($keyModel['public_key'].'1sA5d1gPPms8Oolos') .'=='. $public_key,'db');

        if(md5($keyModel['public_key'].Yii::$app->params['systemSalt']) == $public_key){
            return 'SUCCESS';
        }
        return 'PUBLIC_KEY_ERROR';
    }

    /*
     * 根据设备号生成公钥
     * @param device_number string 设备编号
     * @param type string 设备类型
     * */
    public static function generatePublicKey($device_number,$type){
        try{
            $public_key = ToolsClass::randNumber(32,2);
            $commandSql = Yii::$app->db->createCommand("insert into yl_system_public_key (device_number,public_key) values ('".$device_number."','".$public_key."') ON DUPLICATE KEY UPDATE public_key = '".$public_key."'");
            $commandSql->execute();
            return $public_key;
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return false;
        }
    }
}
