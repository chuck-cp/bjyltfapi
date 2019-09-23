<?php

namespace wap\models;

use Yii;
use yii\web\UrlManager;

/**
 * This is the model class for table "{{%shop}}".
 *
 * @property string $id
 * @property string $member_id
 * @property string $member_name
 * @property string $member_mobile
 * @property string $member_price
 * @property string $member_reward_price
 * @property string $admin_member_id
 * @property string $shop_member_id
 * @property string $wx_member_id
 * @property string $shop_image
 * @property string $name
 * @property string $area
 * @property string $area_name
 * @property string $address
 * @property integer $apply_screen_number
 * @property integer $screen_number
 * @property integer $error_screen_number
 * @property integer $status
 * @property integer $screen_status
 * @property string $create_at
 * @property string $install_finish_at
 * @property string $acreage
 * @property integer $apply_client
 * @property integer $mirror_account
 * @property integer $shop_type
 * @property integer $examine_user_id
 * @property string $examine_user_name
 */
class Shop extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'member_price', 'member_reward_price', 'admin_member_id', 'shop_member_id', 'wx_member_id', 'area', 'apply_screen_number', 'screen_number', 'error_screen_number', 'status', 'screen_status', 'install_status', 'delivery_status', 'apply_client', 'mirror_account', 'shop_type', 'last_examine_user_id', 'examine_number'], 'integer'],
            [['shop_image', 'name', 'area', 'area_name', 'address'], 'required'],
            [['create_at', 'install_finish_at'], 'safe'],
            [['acreage'], 'number'],
            [['member_name'], 'string', 'max' => 50],
            [['member_mobile'], 'string', 'max' => 11],
            [['shop_image', 'area_name', 'address'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 100],
            [['examine_user_group'], 'string', 'max' => 3],
            [['examine_user_name'], 'string', 'max' => 20],
            [['agreement_name'], 'string', 'max' => 60],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'member_name' => 'Member Name',
            'member_mobile' => 'Member Mobile',
            'member_price' => 'Member Price',
            'member_reward_price' => 'Member Reward Price',
            'admin_member_id' => 'Admin Member ID',
            'shop_member_id' => 'Shop Member ID',
            'wx_member_id' => 'Wx Member ID',
            'shop_image' => 'Shop Image',
            'name' => 'Name',
            'area' => 'Area',
            'area_name' => 'Area Name',
            'address' => 'Address',
            'apply_screen_number' => 'Apply Screen Number',
            'screen_number' => 'Screen Number',
            'error_screen_number' => 'Error Screen Number',
            'status' => 'Status',
            'screen_status' => 'Screen Status',
            'install_status' => 'Install Status',
            'delivery_status' => 'Delivery Status',
            'create_at' => 'Create At',
            'install_finish_at' => 'Install Finish At',
            'acreage' => 'Acreage',
            'apply_client' => 'Apply Client',
            'mirror_account' => 'Mirror Account',
            'shop_type' => 'Shop Type',
            'last_examine_user_id' => 'Last Examine User ID',
            'examine_user_group' => 'Examine User Group',
            'examine_user_name' => 'Examine User Name',
            'examine_number' => 'Examine Number',
            'agreement_name' => 'Agreement Name',
        ];
    }
    /*
     * 获取指定用户指定状态下的店铺
     */
    public static function getShopsByMemberStatus($member_id, $install_status){
        if(!is_numeric($member_id)) { return []; }
        return self::find()->where(['member_id'=>$member_id,'install_status'=>$install_status])->andWhere(['<','status',3])->select('shop_image, name, area_name, address, id, status, screen_status, create_at')->orderBy('id desc')->asArray()->all();
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
