<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use Yii;
use yii\data\Pagination;
use yii\db\Expression;

/**
 * This is the model class for table "{{%sign_team_count_shop_detail}}".
 *
 * @property string $id
 * @property string $team_id
 * @property string $shop_id
 * @property string $create_at
 */
class SignTeamCountShopDetail extends ApiActiveRecord
{
    public $page;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sign_team_count_shop_detail}}';
    }

    //获取某日重复签到店铺详情  ,'left(create_at,10)'=>$this->create_at
    public function getRepeatShops(){
        if($this->team_id == 'business'){
            $where = ['create_at'=>$this->create_at];
            $team_name = '全部业务组';
        }else{
            $where = ['create_at'=>$this->create_at, 'team_id'=>$this->team_id];
            $team_name = SignTeam::findOne($this->team_id)->getAttribute('team_name');
        }
        $data = self::find()->where($where)->select('shop_name,team_id,mongo_id,create_at')->orderBy('create_at DESC')->asArray()->all();
        //$pagination = new  Pagination(['totalCount'=>$obj->count(), 'pageSize'=>5]);
        //$pagination->validatePage = false;
        //$data = $obj->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $arr = [];
        if(!empty($data)){
            foreach ($data as $k => $v){
                $arr[$k]['shop_name'] = $v['shop_name'];
                $arr[$k]['items'] = SignBusiness::find()->where(['mongo_id'=>$v['mongo_id'],'substr(create_at,1,10)'=>$this->create_at])->select(new Expression('yl_sign.id,team_id,team_name,member_id,member_name,member_avatar,shop_name,shop_address,substr(yl_sign.create_at,12,5) as tm'))->joinWith('signData',false)->orderBy('tm ASC')->asArray()->all();
            }
        }

        return ['team_name'=>$team_name,'item'=>$arr];
    }
    //获取业务签到表数据
    protected function getBusiness(){
        return $this->hasOne(SignBusiness::className(),['id'=>'business_id']);
    }

    public function scenes(){
        return [
            'repeat-shops' => [
                'create_at' => [
                    'required'=>'1',
                    'result'=>'SIGN_CREATE_AT_EMPTY'
                ],
                'team_id' => [
                    'required'=>'1',
                    'result'=>'SIGN_TEAM_ID_EMPTY'
                ],
            ],
        ];
    }

}
