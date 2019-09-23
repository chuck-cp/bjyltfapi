<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "{{%system_startup}}".
 *
 * @property integer $id
 * @property string $version
 * @property integer $visibility
 * @property string $start_at
 * @property string $end_at
 * @property string $start_pic
 * @property string $link
 * @property integer $create_user_id
 * @property string $create_user_name
 * @property string $create_at
 * @property integer $type
 */
class SystemStartup extends \api\core\ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%system_startup}}';
    }
    /*
     * 获取版本对应的启动页
     */
    public function getStartup(){
        $date = date('Y-m-d H:i:s');
        return self::find()->where(['and',['<=','start_at',$date],['>=','end_at',$date]])->select('start_pic,link')->orderBy('id DESC')->limit(1)->asArray()->one();
        //return self::find()->where(['and',['version'=>$this->version],['<=','start_at',$date],['>=','end_at',$date]])->select('start_pic,link')->orderBy('id DESC')->limit(1)->asArray()->one();
    }
    /*
     * 场景
     */
    public function scenes(){
        return [
            'startup' => [
                'version' => [
                    'required' => 1,
                    'type' => 'string'
                ],
            ],
        ];
    }
}
