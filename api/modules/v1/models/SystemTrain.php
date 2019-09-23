<?php

namespace api\modules\v1\models;

use Yii;

class SystemTrain extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%system_train}}';
    }
    /*
     * 获取资源列表
     */
    public function getMaterialList(){
        $res = self::find()->where(['status'=>1])->select('id, name, type, content, thumbnail')->orderBy('sort')->asArray()->all();
        if(!empty($res)){
            foreach ($res as $k => $v){
                if($v['type'] == 1){
                    $res[$k]['content'] = '/business-trainning?id='.$v['id'];
                }
            }
        }
        return $res;
    }



}
