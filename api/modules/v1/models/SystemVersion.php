<?php

namespace api\modules\v1\models;

use Yii;

/**
 * 版本管理
 */
class SystemVersion extends \api\core\ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%system_version}}';
    }

    public function getVersion(){
        $info = self::find()->where(['app_type'=>$this->app_type,'status'=>1])->select('upgrade_type,version,url,desc,version_type')->orderBy('id desc')->limit(1)->asArray()->one();
        if(!empty($info)){
            if($this->app_type == 1){
                if(substr($info['url'],0,7) !== 'http://' && substr($info['url'],0,8) !== 'https://'){
                    $info['url'] = 'https://'.$info['url'];
                }
            }
        }
        return $info;
    }

    /*
     * 场景
     * */
    public function scenes(){
        return [
            'version'=>[
                'app_type'=>[
                    'required'=>'1',
                    'result'=>'APP_TYPE_EMPTY'
                ],
            ],
        ];
    }
}
