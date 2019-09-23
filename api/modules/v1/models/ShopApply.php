<?php

namespace api\modules\v1\models;

use api\modules\v1\models\ShopHeadquarters;
use api\modules\v1\models\ShopHeadquartersList;
use common\libs\ToolsClass;
use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "{{%shop_apply}}".
 */
class ShopApply extends \api\core\ApiActiveRecord
{

    public $apply_brokerage_token;
    public $shop_member_id;
    public $verify;
    public $install_images;
    public $screen_number;
    public $token;
    public $isupdate;
    public $replace_id;
    public $mirror_account;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop_apply}}';
    }

    public function beforeSave($insert){
        if($insert){
            $this->apply_code = time().ToolsClass::randNumber(4);
            $this->dynamic_code = ToolsClass::randNumber(4,2);
        }
        $this->identity_card_front = ToolsClass::replaceCosUrl($this->identity_card_front);
        $this->panorama_image = ToolsClass::replaceCosUrl($this->panorama_image);
        $this->business_licence = ToolsClass::replaceCosUrl($this->business_licence);
        $this->screen_start_at = str_replace(' ','',$this->screen_start_at);
        $this->screen_end_at = str_replace(' ','',$this->screen_end_at);
        //如果是总店或者分店的话，验证申请人手机号在总店信息中是否匹配,计算首年佣金和首年外每月佣金
        $obj = Shop::findOne($this->id);
        if(!$obj){
            throw new Exception('[error]数据有误');
        }
        $headquarters_list_id = $obj->headquarters_list_id;
        if($headquarters_list_id){
            if($this->mirror_account == 2){
                $this->apply_brokerage = SystemConfig::getConfig('small_shop_price_first_install_apply');
                $this->apply_brokerage_by_month = SystemConfig::getConfig('small_shop_subsidy_price');
            }else{
                $price = ShopHeadquartersList::getBrokerageById($headquarters_list_id);
                $this->apply_brokerage = $price['by_year'];
                $this->apply_brokerage_by_month = $price['by_month'];
            }
            $nu = ShopHeadquarters::find()->where(['mobile'=>$this->apply_mobile,'id'=>$obj->headquarters_id])->count();
            if(!$nu){
                throw new Exception('[error]分店数据有误');
            }
        }
        return parent::beforeSave($insert);
    }

    public function tests()
    {
        try{
            $this->save();
            return true;
        }catch (Exception $e){
            //var_dump($e->getMessage());exit;
            Yii::error($e->getMessage(),'db');
            return false;
        }
    }
    /*
     * 获取我的店铺数量
     * */
    public function getMyShopNumberByMobile(){
        return self::find()->where(['apply_mobile'=>$this->apply_mobile])->count();
    }

    /*
     * 检查申请人手机号
     * */
    public function checkApplyMobile($mobile){
        if(empty($mobile)){
            return true;
        }
        $memberModel = Member::find()->where(['mobile'=>$mobile])->select('id')->asArray()->one();
        if(empty($memberModel)){
            return true;
        }
        $this->shop_member_id = $memberModel['id'];
        return true;
    }

    /*
     * 验证开始时间是否正确
     * */
    public function checkScreenStartAt(){
        $screen_start_at = explode(":",$this->screen_start_at);
        if(count($screen_start_at) !== 2){
            return false;
        }
        if(!in_array($screen_start_at[0],[8,9,10,11,12,13])){
            return false;
        }
        return true;
    }
    /*
     * 验证屏幕关机时间是否正确
     */
    public function checkScreenEndAt(){
        $screen_start_at = explode(":",$this->screen_start_at);
        $screen_end_at = explode(":",$this->screen_end_at);
        if((($screen_end_at[0]*60 + $screen_end_at[1]) - ($screen_start_at[0]*60 + $screen_start_at[1]))/60 < 10){
            return false;
        }
        return true;
    }
    /*
     * 修改店铺信息
     */
    public function modifyShop()
    {
        try{
            $res = $this->save();
            return true;
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return false;
        }
    }

    /*
     * 检查申请人佣金
     * */
    public function checkApplyBrokerageToken($token){
        return $token == ToolsClass::getKeeperBrokerageToken($this->apply_brokerage,$this->apply_brokerage_by_month);
    }

    /*
     * 检查分店信息
     * */
    public function checkBranchShopInfo($headquarters_id){
        $headModel = ShopHeadquarters::find()->where(['id'=>$headquarters_id])->select('mobile')->asArray()->one();
        if(empty($headModel)){
            return false;
        }
        if($headModel['mobile'] !== $this->apply_mobile){
            return false;
        }
        return true;
    }

    /*
     * 检查是否上传授权图片
     * */
    public function checkAuthorizeImage($shop_operate_type){
        if($shop_operate_type == 1){
            //租赁店铺
            if(empty($this->authorize_image)){
                return false;
            }
        }
        return true;
    }
    /*
     * 场景
     * */
    public function scenes()
    {
        return [
            'modify'=>[
                'apply_name'=>[
                    'required'=>'1',
                    'result'=>'APPLY_NAME_EMPTY',
                ],
                'contacts_name'=>[
                    'required'=>'1',
                    'result'=>'CONTACTS_NAME_EMPTY',
                ],
                'contacts_mobile'=>[
                    'required'=>'1',
                    'result'=>'CONTACTS_MOBILE_EMPTY',
                ],
                'identity_card_num'=>[
                    'required'=>'1',
                    'result'=>'IDENTITY_CARD_EMPTY',
                ],
                'company_name'=>[
                    'required'=>'1',
                    'result'=>'COMPANY_NAME_EMPTY'
                ],
                'registration_mark'=>[
                    'required'=>'1',
                    'result'=>'REGISTRATION_MARK_EMPTY'
                ],
                'identity_card_front'=>[
                    'required'=>'1',
                    'result'=>'IDENTITY_CARD_FRONT_EMPTY'
                ],
                'identity_card_back'=>[
                    'required'=>'1',
                    'result'=>'IDENTITY_CARD_BACK_EMPTY'
                ],
                'agent_identity_card_front'=>[],
                'agent_identity_card_back'=>[],
                'business_licence'=>[
                    'required'=>'1',
                    'result'=>'BUSINESS_LICENCE_EMPTY'
                ],
                'panorama_image'=>[
                    'required'=>'1',
                    'result'=>'PANORAMA_IMAGE_EMPTY'
                ],
                'apply_brokerage'=>[
                    'required'=>'1',
                    'result'=>'APPLY_BROKERAGE_EMPTY'
                ],
                'apply_brokerage_by_month'=>[
                    'required'=>'1',
                    'result'=>'APPLY_BROKERAGE_BY_MONTH_EMPTY'
                ],
                'apply_brokerage_token'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'APPLY_BROKERAGE_TOKEN_EMPTY'
                        ],
                        [
                            'function'=>'this::checkApplyBrokerageToken',
                            'result'=>'APPLY_BROKERAGE_ERROR'
                        ]
                    ]
                ],
                'screen_start_at'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'SCREEN_START_AT_EMPTY'
                        ],
                        [
                            'function'=>'this::checkScreenStartAt',
                            'result'=>'SCREEN_START_AT_ERROR'
                        ]
                    ]
                ],
                'screen_end_at'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'SCREEN_END_AT_EMPTY'
                        ],
                        [
                            'function'=>'this::checkScreenEndAt',
                            'result'=>'SCREEN_END_AT_ERROR'
                        ]
                    ]
                ],

                'authorize_image'=>[
                    'default'=>''
                ],
                'other_image'=>[
                    'default' => ''
                ],
            ],


            'create'=>[
                    'apply_name'=>[
                        'required'=>'1',
                        'result'=>'APPLY_NAME_EMPTY',
                    ],
                    'contacts_name'=>[
                        'required'=>'1',
                        'result'=>'CONTACTS_NAME_EMPTY',
                    ],
                    'contacts_mobile'=>[
                        'required'=>'1',
                        'result'=>'CONTACTS_MOBILE_EMPTY',
                    ],
                    'identity_card_num'=>[
                        'required'=>'1',
                        'result'=>'IDENTITY_CARD_EMPTY',
                    ],
                    'apply_mobile'=>[
                        [
                            [
                                'required'=>'1',
                                'result'=>'APPLY_MOBILE_EMPTY',
                            ],
                            [
                                'function'=>'this::checkApplyMobile',
                            ]
                        ]
                    ],
                    'company_name'=>[
                        'required'=>'1',
                        'result'=>'COMPANY_NAME_EMPTY'
                    ],
                    'registration_mark'=>[
                        'required'=>'1',
                        'result'=>'REGISTRATION_MARK_EMPTY'
                    ],
                    'identity_card_front'=>[
                        'required'=>'1',
                        'result'=>'IDENTITY_CARD_FRONT_EMPTY'
                    ],
                    'identity_card_back'=>[
                        'required'=>'1',
                        'result'=>'IDENTITY_CARD_BACK_EMPTY'
                    ],
                    'agent_identity_card_front'=>[],
                    'agent_identity_card_back'=>[],
                    'mirror_account' => [],
                    'business_licence'=>[
                        'required'=>'1',
                        'result'=>'BUSINESS_LICENCE_EMPTY'
                    ],
                    'panorama_image'=>[
                        'required'=>'1',
                        'result'=>'PANORAMA_IMAGE_EMPTY'
                    ],
                    'apply_brokerage'=>[
                        'required'=>'1',
                        'result'=>'APPLY_BROKERAGE_EMPTY'
                    ],
                    'apply_brokerage_by_month'=>[
                        'required'=>'1',
                        'result'=>'APPLY_BROKERAGE_BY_MONTH_EMPTY'
                    ],
                    'apply_brokerage_token'=>[
                        [
                            [
                                'required'=>'1',
                                'result'=>'APPLY_BROKERAGE_TOKEN_EMPTY'
                            ],
                            [
                                'function'=>'this::checkApplyBrokerageToken',
                                'result'=>'APPLY_BROKERAGE_ERROR'
                            ]
                        ]
                    ],
                'screen_start_at'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'SCREEN_START_AT_EMPTY'
                        ],
                        [
                            'function'=>'this::checkScreenStartAt',
                            'result'=>'SCREEN_START_AT_ERROR'
                        ]
                    ]
                ],
                //新加关机时间2018913
                'screen_end_at'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'SCREEN_END_AT_EMPTY'
                        ],
                        [
                            'function'=>'this::checkScreenEndAt',
                            'result'=>'SCREEN_END_AT_ERROR'
                        ]
                    ]
                ],
                'authorize_image'=>[
                    'default'=>''
                ],
                'other_image'=>[
                    'default' => ''
                ],
            ],
            'shop_apply_modify'=>[
                'contacts_name'=>[
                    'required'=>'1',
                    'result'=>'CONTACTS_NAME_EMPTY',
                ],
                'contacts_mobile'=>[
                    'required'=>'1',
                    'result'=>'CONTACTS_MOBILE_EMPTY',
                ],
                'agent_identity_card_front'=>[],
                'agent_identity_card_back'=>[],
                'mirror_account' => [],
                'business_licence'=>[
                    'required'=>'1',
                    'result'=>'BUSINESS_LICENCE_EMPTY'
                ],
                'panorama_image'=>[
                    'required'=>'1',
                    'result'=>'PANORAMA_IMAGE_EMPTY'
                ],
                'apply_brokerage'=>[
                    'required'=>'1',
                    'result'=>'APPLY_BROKERAGE_EMPTY'
                ],
                'apply_brokerage_by_month'=>[
                    'required'=>'1',
                    'result'=>'APPLY_BROKERAGE_BY_MONTH_EMPTY'
                ],
                'apply_brokerage_token'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'APPLY_BROKERAGE_TOKEN_EMPTY'
                        ],
                        [
                            'function'=>'this::checkApplyBrokerageToken',
                            'result'=>'APPLY_BROKERAGE_ERROR'
                        ]
                    ]
                ],
                'screen_start_at'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'SCREEN_START_AT_EMPTY'
                        ],
                        [
                            'function'=>'this::checkScreenStartAt',
                            'result'=>'SCREEN_START_AT_ERROR'
                        ]
                    ]
                ],
                //新加关机时间2018913
                'screen_end_at'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'SCREEN_END_AT_EMPTY'
                        ],
                        [
                            'function'=>'this::checkScreenEndAt',
                            'result'=>'SCREEN_END_AT_ERROR'
                        ]
                    ]
                ],
                'authorize_image'=>[
                    'default'=>''
                ],
                'other_image'=>[
                    'default' => ''
                ],
            ],
            'branch-create'=>[
                'authorize_image' => [
                    'default'  => '',
                ],
                'apply_name'=>[
                    'required'=>'1',
                    'result'=>'APPLY_NAME_EMPTY',
                ],
                'contacts_name'=>[
                    'required'=>'1',
                    'result'=>'CONTACTS_NAME_EMPTY',
                ],
                'contacts_mobile'=>[
                    'required'=>'1',
                    'result'=>'CONTACTS_MOBILE_EMPTY',
                ],
                'identity_card_num'=>[
                    'required'=>'1',
                    'result'=>'IDENTITY_CARD_EMPTY',
                ],
                'apply_mobile'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'APPLY_MOBILE_EMPTY',
                        ],
                        [
                            'function'=>'this::checkApplyMobiles',
                        ]
                    ]
                ],
                'company_name'=>[
                    'required'=>'1',
                    'result'=>'COMPANY_NAME_EMPTY'
                ],
                'registration_mark'=>[
                    'required'=>'1',
                    'result'=>'REGISTRATION_MARK_EMPTY'
                ],
                'identity_card_front'=>[
                    'required'=>'1',
                    'result'=>'IDENTITY_CARD_FRONT_EMPTY'
                ],
                'identity_card_back'=>[
                    'required'=>'1',
                    'result'=>'IDENTITY_CARD_BACK_EMPTY'
                ],
                'agent_identity_card_front'=>[],
                'agent_identity_card_back'=>[],
                'business_licence'=>[
                    'required'=>'1',
                    'result'=>'BUSINESS_LICENCE_EMPTY'
                ],
                'panorama_image'=>[
                    'required'=>'1',
                    'result'=>'PANORAMA_IMAGE_EMPTY'
                ],
                'apply_brokerage'=>[],
                'apply_brokerage_by_month'=>[],
                'screen_start_at'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'SCREEN_START_AT_EMPTY'
                        ],
                        [
                            'function'=>'this::checkScreenStartAt',
                            'result'=>'SCREEN_START_AT_ERROR'
                        ]
                    ]
                ],
                'screen_end_at'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'SCREEN_END_AT_EMPTY'
                        ],
                        [
                            'function'=>'this::checkScreenEndAt',
                            'result'=>'SCREEN_END_AT_ERROR'
                        ]
                    ]
                ],
                'other_image'=>[
                    'default'=>''
                ],
            ],
                'existence'=>[
                    'apply_code'=>[
                        'required'=>'1',
                        'result'=>'APPLY_CODE'
                    ],
                    'dynamic_code'=>[
                        'required'=>'1',
                        'result'=>'DYNAMIC_CODE'
                    ]
                ],
                'get'=>[
                    'id'=>[
                        'required'=>'1',
                        'result'=>'APPLY_CODE'
                    ],
                    'apply_code'=>[
                        'required'=>'1',
                        'result'=>'APPLY_CODE'
                    ],
                    'dynamic_code'=>[
                        'required'=>'1',
                        'result'=>'DYNAMIC_CODE'
                    ]
                ],
                'post'=>[
                    'install_name'=>[],
                    'install_mobile'=>[],
                    'install_images'=>[],
                    'id'=>[],
                    'verify'=>[
                        'required'=>'1',
                        'result'=>'VERIFY_EMPTY',
                    ],
                    'apply_code'=>[
                        'required'=>'1',
                        'result'=>'APPLY_CODE'
                    ],
                    'dynamic_code'=>[
                        'required'=>'1',
                        'result'=>'DYNAMIC_CODE'
                    ]
                ],
                'activation'=>[
                    'id'=>[
                        'required'=>'1',
                        'result'=>'APPLY_CODE'
                    ],
                    'apply_code'=>[
                        'required'=>'1',
                        'result'=>'APPLY_CODE'
                    ],
                    'dynamic_code'=>[
                        'required'=>'1',
                        'result'=>'DYNAMIC_CODE'
                    ]
                ],
                'unline'=>[
                    'install_name'=>[],
                    'install_mobile'=>[],
                    'install_images'=>[],
                    'id'=>[],
                    'verify'=>[
                        'required'=>'1',
                        'result'=>'VERIFY_EMPTY',
                    ],
                    'token'=>[],
                    'isupdate'=>[]
                ],
                'activationunline'=>[
                    'id'=>[
                        'required'=>'1',
                        'result'=>'APPLY_CODE'
                    ],
                    'token'=>[]
                ],
                'underlinecheck'=>[
                    'id'=>[
                        'required'=>'1',
                        'result'=>'APPLY_CODE'
                    ],
                    'token'=>[]
                ],
                'underlineimgupdate'=>[
                    'install_name'=>[],
                    'install_mobile'=>[],
                    'install_images'=>[],
                    'id'=>[],
                    'token'=>[]
                ],
                'screencheck'=>[
                    'id'=>[
                        'required'=>'1',
                        'result'=>'APPLY_CODE'
                    ],
                    'token'=>[],
                    'replace_id'=>[],
                ],
                'screeninster'=>[
                    'install_name'=>[],
                    'install_mobile'=>[],
                    'install_images'=>[],
                    'id'=>[],
                    'token'=>[],
                    'isupdate'=>[]
                ],
            'screen-incr'=>[
                'install_name'=>[],
                'install_mobile'=>[],
                'install_images'=>[],
                'id'=>[],
                'token'=>[],
                'isupdate'=>[],
                'replace_id' => [],
            ],
                'screenactivation'=>[
                'id'=>[
                    'required'=>'1',
                    'result'=>'APPLY_CODE'
                    ],
                'token'=>[]
                ],
                'screenimgupdate'=>[
                    'install_images'=>[],
                    'id'=>[],
                    'token'=>[],
                    'replace_id' => [],
                ],

        ];
    }
    /*
    * 验证安装订单 是否 存在
    * */
    public function getExistence()
    {
        return self::find()->where(['apply_code'=>$this->apply_code,'dynamic_code'=>$this->dynamic_code])->select('id')->asArray()->one();
    }
    /*
      * 获取安装订单详情
      * */
    public function getShopOrder($id){
        $applyModel = self::find()->where(['id'=>$id])->select('apply_code,apply_name,apply_mobile,company_name,panorama_image,screen_start_at,screen_end_at,identity_card_front,identity_card_back,agent_identity_card_front,agent_identity_card_back,identity_card_num,contacts_name,contacts_mobile,registration_mark,business_licence,authorize_image,other_image')->asArray()->one();
        if(empty($applyModel)){
            return [];
        }
        $applyModel['other_image'] = explode(',',$applyModel['other_image']);
        $applyModel['authorize_image'] = explode(',',$applyModel['authorize_image']);
        $applyModel['agent_identity_card_picture'] = [];
        if($applyModel['agent_identity_card_front']){
            $applyModel['agent_identity_card_picture'][] = $applyModel['agent_identity_card_front'];
        }
        if($applyModel['agent_identity_card_back']){
            $applyModel['agent_identity_card_picture'][] = $applyModel['agent_identity_card_back'];
        }
        $shopModel = Shop::find()->where(['id'=>$id])->select('id,member_id,shop_operate_type,area_name,address,name,acreage,screen_number,mirror_account,shop_image,status,install_status,screen_status,install_member_id,install_team_id,install_member_name,install_mobile,apply_screen_number,member_name,member_mobile,shop_operate_type,introducer_member_name,introducer_member_mobile')->asArray()->one();
        if(empty($shopModel)){
            return [];
        }
        return array_merge($applyModel,$shopModel);
    }
    /*
    * 获取店铺安装反馈详细信息
    */
    public function getShopimginfo($id){
        $applyModel = self::find()->where(['id'=>$id])->select('install_name,install_mobile')->asArray()->one();
        if(empty($applyModel)){
            return [];
        }
        $screenModel = Screen::find()->where(['shop_id'=>$id])->select('id,software_number,image')->asArray()->all();
        if(empty($screenModel)){
            return [];
        }
        $applyModel['screen']=$screenModel;
        return  array_merge($applyModel);
    }
    /*
      * 屏幕安装 获取店铺安装反馈详细信息
      */
    public function getScreenShopimginfo($id,$replace_id=0){
        $where = [];
        if($replace_id){
            $where = ['shop_id'=>$id,'replace_id'=>$replace_id];
        }else{
            $where = ['shop_id'=>$id];
        }
        $shopData = Shop::find()->where(['id'=>$id])->select('install_member_id,screen_number,install_team_id,install_member_name,install_mobile')->asArray()->one();
        if(empty($shopData)){
            return [];
        }
        //如果是换屏去replace表里查询安装人信息，否则再去shop表里查询安装人信息
        //$replace = ShopScreenReplace::find()->where(['shop_id'=>$id]);

        if($shopData['install_team_id']>0){
            $teamModel=MemberTeam::find()->where(['id'=>$shopData['install_team_id']])->select('team_member_id')->asArray()->one();
            $memberModel=Member::find()->where(['id'=>$teamModel['team_member_id']])->select('name,mobile')->asArray()->one();
            $shopData['team_member_name']=$memberModel['name'];
            $shopData['team_member_mobile']=$memberModel['mobile'];
        }

        $shopscreenModel = Screen::find()->where($where)->select('id,software_number,image')->asArray()->all();

        if(empty($shopscreenModel)){
            return [];
        }
        $shopData['screen']=$shopscreenModel;
        return $shopData;
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
