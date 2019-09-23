<?php

namespace wap\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * 版本管理
 */
class SystemVersion extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%system_version}}';
    }

    public function getVersionUrl($app_type=1){
        $versionModel = self::find()->where(['app_type'=>$app_type,'status'=>1])->select('url')->orderBy('id desc')->limit(1)->asArray()->one();
        if(empty($versionModel)){
            return '';
        }
        return $versionModel['url'];
    }
}
