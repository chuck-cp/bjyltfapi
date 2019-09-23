<?php

namespace wap\models;

use Yii;

/**
 * This is the model class for table "{{%shop_headquarters}}".
 *
 * @property string $id
 * @property string $name
 * @property string $mobile
 * @property string $member_id
 * @property string $identity_card_num
 * @property string $identity_card_front
 * @property string $identity_card_back
 * @property string $company_name
 * @property string $company_area_id
 * @property string $company_area_name
 * @property string $company_address
 * @property string $registration_mark
 * @property string $business_licence
 * @property string $agreement_name
 * @property string $create_at
 */
class ShopHeadquarters extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop_headquarters}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'mobile', 'identity_card_num', 'company_name', 'company_area_id', 'company_area_name', 'company_address', 'registration_mark', 'business_licence'], 'required'],
            [['member_id', 'company_area_id'], 'integer'],
            [['create_at'], 'safe'],
            [['name', 'registration_mark'], 'string', 'max' => 50],
            [['mobile'], 'string', 'max' => 16],
            [['identity_card_num'], 'string', 'max' => 18],
            [['identity_card_front', 'identity_card_back', 'company_area_name', 'company_address', 'business_licence'], 'string', 'max' => 255],
            [['company_name'], 'string', 'max' => 100],
            [['agreement_name'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'mobile' => 'Mobile',
            'member_id' => 'Member ID',
            'identity_card_num' => 'Identity Card Num',
            'identity_card_front' => 'Identity Card Front',
            'identity_card_back' => 'Identity Card Back',
            'company_name' => 'Company Name',
            'company_area_id' => 'Company Area ID',
            'company_area_name' => 'Company Area Name',
            'company_address' => 'Company Address',
            'registration_mark' => 'Registration Mark',
            'business_licence' => 'Business Licence',
            'agreement_name' => 'Agreement Name',
            'create_at' => 'Create At',
        ];
    }
}
