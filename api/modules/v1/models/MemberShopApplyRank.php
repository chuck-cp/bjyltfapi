<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%member_shop_apply_rank}}".
 *
 * @property string $id
 * @property string $member_id
 * @property integer $last_half_past_month_shop_number
 * @property string $last_half_past_month_screen_number
 * @property integer $last_week_shop_number
 * @property string $last_week_screen_number
 * @property integer $week_shop_number
 * @property string $week_screen_number
 * @property integer $month_shop_number
 * @property string $month_screen_number
 * @property integer $last_month_shop_number
 * @property string $last_month_screen_number
 * @property integer $count_shop_number
 * @property string $count_screen_number
 */
class MemberShopApplyRank extends ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_shop_apply_rank}}';
    }
    /*
     * 获取不同时间段的排名
     */
    public function getRankByTime($time = 1){
        switch ($time){
            case 1:
                $field = 'month_shop_number, month_screen_number';
                $order = 'month_shop_number DESC';
                break;
            case 2:
                $field = 'last_month_shop_number, last_month_screen_number';
                $order = 'last_month_shop_number DESC';
                break;
            case 3:
                $field = 'last_half_past_month_shop_number, last_half_past_month_screen_number';
                $order = 'last_half_past_month_shop_number DESC';
                break;
            case 4:
                $field = 'last_week_shop_number, last_week_screen_number';
                $order = 'last_week_shop_number DESC';
                break;
            case 5:
                $field = 'count_shop_number, count_screen_number';
                $order = 'count_shop_number DESC';
                break;
        }
        $order .= ', wait_install_shop_number DESC';
        $rank = self::find()->select('member_id,wait_install_shop_number,'.$field)->orderBy($order)->asArray()->all();
        if(!empty($rank)){
            foreach ($rank as $k => $v){
                $member = Member::find()->where(['id'=>$v['member_id']])->select('name,avatar,inside')->asArray()->one();
                if(!empty($member)){
                    if($member['inside'] == 0){
                        unset($rank[$k]);
                    }else{
                        $rank[$k]['member_name'] = $member['name'];
                        $rank[$k]['tx'] = $member['avatar'];
                    }
                }else{
                    $rank[$k]['member_name'] = '---';
                    $rank[$k]['tx'] = '';
                }
            }
        }
        return $rank;

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
