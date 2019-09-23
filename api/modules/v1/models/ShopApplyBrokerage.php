<?php

namespace api\modules\v1\models;

use xplqcloud\cos\Api;
use Yii;
use api\core\ApiActiveRecord;
/**
 * This is the model class for table "{{%shop_apply_brokerage}}".
 *
 * @property string $id
 * @property string $shop_id
 * @property string $shop_name
 * @property string $area_name
 * @property string $address
 * @property string $area_id
 * @property string $apply_id
 * @property string $apply_name
 * @property string $apply_mobile
 * @property integer $screen_number
 * @property integer $mirror_number
 * @property string $price
 * @property integer $grant_status
 * @property string $date
 * @property string $create_at
 * @property string $install_finish_at
 */
class ShopApplyBrokerage extends ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop_apply_brokerage}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shop_id', 'shop_name', 'area_name', 'area_id', 'apply_name', 'apply_mobile'], 'required'],
            [['shop_id', 'area_id', 'apply_id', 'screen_number', 'mirror_number', 'price', 'grant_status', 'date'], 'integer'],
            [['create_at', 'install_finish_at'], 'safe'],
            [['shop_name'], 'string', 'max' => 255],
            [['area_name', 'apply_name'], 'string', 'max' => 50],
            [['address'], 'string', 'max' => 200],
            [['apply_mobile'], 'string', 'max' => 16],
            [['shop_id', 'date'], 'unique', 'targetAttribute' => ['shop_id', 'date'], 'message' => 'The combination of Shop ID and Date has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shop_id' => 'Shop ID',
            'shop_name' => 'Shop Name',
            'area_name' => 'Area Name',
            'address' => 'Address',
            'area_id' => 'Area ID',
            'apply_id' => 'Apply ID',
            'apply_name' => 'Apply Name',
            'apply_mobile' => 'Apply Mobile',
            'screen_number' => 'Screen Number',
            'mirror_number' => 'Mirror Number',
            'price' => 'Price',
            'grant_status' => 'Grant Status',
            'date' => 'Date',
            'create_at' => 'Create At',
            'install_finish_at' => 'Install Finish At',
        ];
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
