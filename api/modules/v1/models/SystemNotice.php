<?php

namespace api\modules\v1\models;

use Yii;

class SystemNotice extends \api\core\ApiActiveRecord
{
    public static function tableName()
    {
        return '{{%system_notice}}';
    }

    /*
     * 获取首页公告
     * */
    public function getIndexNotice(){
        return self::find()->select('id,title')->orderBy('top desc,id desc')->limit(1)->asArray()->all();
    }
}
