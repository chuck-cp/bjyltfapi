<?php

namespace wap\models;

use common\libs\ToolsClass;
use Yii;

/**
 * This is the model class for table "{{%shop_apply}}".
 */
class ShopApply extends \api\core\ApiActiveRecord
{

    public $end_screen_at;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop_apply}}';
    }


}
