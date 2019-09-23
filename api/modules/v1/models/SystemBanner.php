<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "{{%system_banner}}".
 *
 * @property integer $id
 * @property string $image_url
 */
class SystemBanner extends \api\core\ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%system_banner}}';
    }
    /*
     * 获取首页banner
     * @param type int banner类型(1、业务系统 2、广告系统)
     * */
    public function getIndexBanner($type=1){
        return self::find()->select('image_url,link_url,target')->where(['type'=>$type])->orderBy('sort asc')->asArray()->all();
    }

    /**
     * Returns static class instance, which can be used to obtain meta information.
     * @param bool $refresh whether to re-create static instance even, if it is already cached.
     * @return static class instance.
     */
    public static function instance($refresh = false)
    {
        // TODO: Implement instance() method.
    }
}
