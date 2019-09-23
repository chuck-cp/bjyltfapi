<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use common\libs\ToolsClass;
use Yii;
use yii\base\Exception;
use yii\data\Pagination;

/**
 * This is the model class for table "{{%member_reward_member}}".
 *
 * @property string $id
 * @property string $b_member_id
 * @property string $member_id
 * @property string $shop_id
 * @property string $head_id
 * @property string $nickname
 * @property string $mobile
 * @property string $software_number
 * @property string $reward_price
 * @property string $create_at
 */
class MemberRewardMember extends ApiActiveRecord
{
    public $page_size = 5;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_reward_member}}';
    }
    public function updateName(){
        $currentObj = self::find()->where(['id'=>$this->id, 'member_id'=>Yii::$app->user->id])->one();
        if(!$currentObj){
            return 'ERROR';
        }
        $currentObj->nickname = $this->nickname;
        try{
            $currentObj->save();
            return 'SUCCESS';
        }catch (Exception $e){
            Yii::error($e->getMessage(),'error');
            return 'ERROR';
        }
    }
    public function checkRewardName(){
        if(strlen(mb_strlen($this->nickname,'UTF8')) > 20){
            return false;
        }
        return true;
    }
    //获取奖励金列表
    public function getRewardList(){
        $where = [];
        $result = [];
        $result['branch_list'] = [];
        if(isset($this->head_id) && $this->head_id){
            $where['head_id'] = $this->head_id;
            //若有分店，分店列表
            $result['branch_list'] = Shop::getShopByHeadId($this->head_id);
        }elseif (isset($this->shop_id) && $this->shop_id){
            $where['shop_id'] = $this->shop_id;
        }
        //昨日奖励金和总奖励金
        $accountInfo = MemberRewardAcountDetail::getTotalAccount($where);
        $result['yestoday_reward'] = strval(ToolsClass::priceConvert(MemberRewardAcountDetail::getYestodayAccount($accountInfo['account_id'])));
        $result['total_reward'] = strval(ToolsClass::priceConvert($accountInfo['reward_price']));

        /*
        $where['member_id'] = Yii::$app->user->id;
        $listObj = self::find()->where($where)->select('yl_member_reward_member.id,member_id,shop_id,shop_name,nickname,mobile,software_number,reward_price,create_at')->orderBy('id DESC');
        $pagination = new Pagination(['totalCount'=>$listObj->count(),'pageSize'=>$this->page_size]);
        $pagination->validatePage = false;
        $data = $listObj->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();

        if(!empty($data)){
            foreach ($data as $k => $v){
                $data[$k]['reward_price'] = ToolsClass::priceConvert($v['reward_price']);
            }
        }
        $result['items'] = $data;
        */
        return $result;
    }
    public function checkRewardId(){
        if(!self::findOne($this->id)){
            return false;
        }
        return true;
    }
    //判断shop_id head_id
    public function checkRewardSid(){
        if(isset($this->shop_id) && isset($this->head_id)){
            return false;
        }
        if(!isset($this->shop_id) && !isset($this->head_id)){
            return false;
        }
        return true;
    }
    //获取某订单列表
    public function getOrderList(){
        $result = [];
        $dataObj = MemberRewardDetail::find()->where(['reward_member_id'=>$this->id,'member_id'=>Yii::$app->user->id]);
        $obj = $dataObj->orderBy('id DESC');
        $result['order_total'] = strval(ToolsClass::priceConvert($obj->sum('order_price')));
        $result['reward_total'] = strval(ToolsClass::priceConvert($obj->sum('reward_price')));
        $pagination = new Pagination(['totalCount'=>$obj->count(), 'pageSize'=>$this->page_size]);
        $pagination->validatePage = false;
        $data = $obj->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        if(!empty($data)){
            foreach ($data as $k => $v){
               $data[$k]['reward_price']  = strval(ToolsClass::priceConvert($v['reward_price']));
               $data[$k]['order_price']  = strval(ToolsClass::priceConvert($v['order_price']));
               $data[$k]['finish_at'] = substr($v['finish_at'],0,10);
               $data[$k]['order_id'] = substr_replace($v['order_id'],'******',3,-3);
            }
        }
        $result['items'] = $data;
        return $result;
    }
    //获取全部订单列表
    public function getAllOrder(){
        $result = [];
        $where = [];
        if(isset($this->head_id) && $this->head_id){
            $where['shop_id'] = $this->head_id;
            $where['shop_type'] = 2;
        }elseif (isset($this->shop_id) && $this->shop_id){
            $where['shop_id'] = $this->shop_id;
            $where['shop_type'] = 1;
        }
        $where['member_id'] = Yii::$app->user->id;
        $obj = MemberRewardDetail::find()->where($where)->orderBy('id DESC');
        $result['order_total'] = strval(ToolsClass::priceConvert($obj->sum('order_price')));
        $result['reward_total'] = strval(ToolsClass::priceConvert($obj->sum('reward_price')));
        $pagination = new Pagination(['totalCount'=>$obj->count(), 'pageSize'=>$this->page_size]);
        $pagination->validatePage = false;
        $data = $obj->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        if(!empty($data)){
            foreach ($data as $k => $v){
                $data[$k]['reward_price']  = strval(ToolsClass::priceConvert($v['reward_price']));
                $data[$k]['order_price']  = strval(ToolsClass::priceConvert($v['order_price']));
                $data[$k]['order_id'] = substr_replace($v['order_id'],'******',3,-3);
            }
        }
        $result['items'] = $data;
        return $result;
    }

    public function shopSearch(){
        $where = [];
        if(isset($this->shop_id) && $this->shop_id){
            $where['shop_id'] = $this->shop_id;
        }
        if(isset($this->head_id) && $this->head_id){
            $where['head_id'] = $this->head_id;
        }
        $where['member_id'] = Yii::$app->user->id;
        $andWhere = [];
        if(isset($this->mobile) && $this->mobile){
            $andWhere = ['or',['like','mobile',$this->mobile],['like','nickname',$this->mobile]];
        }
        $order = '';
        if(isset($this->create_at) && $this->create_at){
            switch ($this->create_at){
                case 1:
                    $order = 'reward_price DESC,';
                    break;
                case 2:
                    $order = 'reward_price ASC,';
                    break;
                case 3:
                    $order = 'create_at DESC,';
                    break;
                case 4:
                    $order = 'create_at ASC,';
                    break;
            }
        }
        $dataObj = self::find()->where($where)->andWhere($andWhere)->orderBy($order.' id DESC');
        $pagination = new Pagination(['totalCount'=>$dataObj->count(), 'pageSize'=>$this->page_size]);
        $pagination->validatePage = false;
        $data = $dataObj->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        if(!empty($data)){
            foreach ($data as $k => $v){
                $data[$k]['reward_price']  = strval(ToolsClass::priceConvert($v['reward_price']));
            }
        }
        return $data;

    }
    public function scenes(){
        return [
            //修改昵称
            'update-name' => [
                'id' => [
                   [
                       [
                           'required' => 1,
                           'result' => 'REWARD_MEMBER_ID_EMPTY',
                       ],
                       [
                           'function' => 'this::checkRewardId',
                           'result' => 'REWARD_MEMBER_NOT_EXIST'
                       ],
                   ]
                ],
                'nickname' => [
                    [
                        [
                            'required' => 1,
                            'result' => 'REWARD_NICK_NAME_EMPTY',
                        ],
                        [
                            'function' => 'this::checkRewardName',
                            'result' => 'REWARD_NICK_NAME_TOO_LONG'
                        ],
                    ]
                ],
            ],
            //奖励金列表
            'reward-list' => [
                'shop_id' => [
                    [
                        'function' => 'this::checkRewardSid',
                        'result' => 'REWARD_SHOP_ID_ERROR'
                    ],
                ],
                'head_id' => [
                    [
                        'function' => 'this::checkRewardHid',
                        'result' => 'REWARD_SHOP_ID_ERROR'
                    ],
                ],
            ],
            //
            'order-list' => [
                'id' => [
                    'required' => 1,
                    'result' => 'REWARD_ID_EMPTY',
                ],
            ],
            //店铺奖励金
            'all-orders-list' => [
                'head_id' => [],
                'shop_id' => [],
                //'shop_type' => [],
            ],
            //
            'shop-search' => [
                'head_id' => [],
                'shop_id' => [],
                'mobile' => [],
                'create_at' => [],

            ],
            'head_search' => [
                'head_id' => [
                    'required' => 1,
                    'result' => 'REWARD_HEAD_ID_EMPTY',
                ],

                'mobile' => [],
            ],
        ];
    }
}
