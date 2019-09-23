<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "{{%member_shop_count}}".
 *
 * @property integer $member_id
 * @property integer $admin_screen_number
 * @property integer $admin_shop_number
 */
class MemberShopCount extends \api\core\ApiActiveRecord
{

    public static function tableName()
    {
        return '{{%member_shop_count}}';
    }

    public static function updateOrCreate($screen_number,$shop_number){
        $member_id = Yii::$app->user->id;
        if($countModel = MemberShopCount::findOne($member_id)){
            $countModel->admin_screen_number += $screen_number;
            $countModel->admin_shop_number += $shop_number;
            return $countModel->save();
        }
        $countModel = new MemberShopCount();
        $countModel->admin_screen_number = $screen_number;
        $countModel->admin_shop_number = $shop_number;
        $countModel->member_id = $member_id;
        return $countModel->save();
    }

}
