<?php

namespace wap\models;

use Yii;

/**
 * 系统配置
 */
class SystemConfig extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%system_config}}';
    }

    public function getConfigById($id){
        return self::find()->where(['id'=>$id])->select('content')->asArray()->one();
    }

    public function getAllConfig($id){
        $configs = self::find()->where(['id'=>$id])->select('id,content')->asArray()->all();
        if(empty($configs)){
            return [];
        }
        foreach($configs as $c){
            $resultConfig[$c['id']] = $c['content'];
        }
        return $resultConfig;
    }
}
