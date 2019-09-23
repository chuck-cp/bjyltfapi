<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use api\core\MongoActiveRecord;
use common\libs\Redis;
use common\libs\ToolsClass;
use Yii;
use yii\data\Pagination;
use yii\db\Exception;
use yii\db\Expression;

/**
 * This is the model class for table "{{%sign_business}}".
 *
 * @property string $id
 * @property string $team_id
 * @property string $member_id
 * @property string $shop_name
 * @property string $shop_acreage
 * @property integer $shop_mirror_number
 * @property string $shop_address
 * @property string $longitude
 * @property string $latitude
 * @property integer $minimum_charge
 * @property string $mobile
 * @property integer $shop_type
 * @property string $screen_brand_name
 * @property integer $screen_number
 * @property string $description
 * @property integer $frist_sign
 * @property integer $late_sign
 * @property string $create_at
 */
class SignBusiness extends MongoActiveRecord
{
    public $member_type;
    public $address_search;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sign_business}}';
    }

    // 签到完成以后的操作
    public function afterCreateSign() {
        try {
            $maintainData = [
                'type' => 'business',
                'longitude' => $this->longitude,
                'latitude' => $this->latitude,
                'sign_id' => $this->id
            ];
            Redis::getInstance(1)->lpush('list_json_sign_coordinate_convert',json_encode($maintainData));
            return true;
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            return false;
        }
    }

    //查询mongodb中有无此店铺
    public function queryShopInMongo($shop_name,$sign_date){
        $where = array(
            'name' => array(
                '$regex' => $shop_name
            ),
            'sign_date' => array(
                '$regex' => $sign_date
            ),
            'loc' => array(
                '$near' => array(
                    '$geometry' => array(
                        'type' => 'Point',
                        'coordinates' => array(floatval($this->longitude), floatval($this->latitude)),
                    ),
                    '$maxDistance' => 500,
                )
            )
        );
        return $this->mongoFindAll('sign_shop',$where);
    }
    //向mongodb guanggao库的 sign集合中写入店铺
    public function addShopToMongoCollection($shop_name,$shop_address){
        $data = [
            'name' => $shop_name,
            'address' => $shop_address,
            'shop_type' => $this->shop_type,
            'sign_date' => date('Y-m-d'),
            'loc' => [
                'type' => 'Point',
                'coordinates' => [
                    floatval($this->longitude),
                    floatval($this->latitude),
                ]
            ],
        ];
        return $this->mongoInsert('sign_shop',$data);
    }
    //删除mongodb文档
    public function delteMongodbDocument($where){
        $this->mongoDelete('sign_shop',$where);
    }
    public function delteMongodbs($where){
        $this->mongoDelete('shop',$where);
    }
    //删除重复的mongo数据
    public function deleteRepeatMongo(){
        return $this->mongoFindAll('shop',[],['limit'=>20]);
        $res = $this->mongoFindAll('shop',[],['limit'=>1000]);
        if(!empty($res)){
            foreach ($res as $k => $v){
                if($k > 0){
                    if($v['name'] == $res[$k-1]['name']){
                        //$del = $this->delteMongodbs(['_id'=>$v['_id']]);
                        //print_r($del."\n");
                        echo "删除成功\n";
                    }else{
                        echo $v['_id']."\n";
                        echo "不相同\n";
                    }
                }
            }
        }
    }
    //业务签到店铺名字判重
    public function judgeRepeatShop($arr,$shop_name){
        if(empty($arr)){ return false; }
        foreach ($arr as $k => $v){
            if($this->judgeRepeatStr($v['name'], $shop_name)){
                return $v['_id'];
            }
            return false;
        }
    }
    //判断字符串重复
    public function judgeRepeatStr($str1,$str2){
        if(!$str1 || !$str2){ return false; }
        if(preg_match('/[a-zA-Z]+/',$str2)){
            //若被检测的字符中含有字母
            if(str_replace(' ','',$str1) !== str_replace(' ','',$str2)){
                return false;
            }
            return true;
        }else{
            //如果没有英文字母
            $str1 = str_replace(['造型','美容美发',' '],'',$str1);
            $str2 = str_replace(['造型','美容美发',' '],'',$str2);
            if(mb_strlen($str2,'UTF-8') < 5){
                if($str2 !== $str1){
                    return false;
                }
                return true;
            }
            $intersect = array_intersect(ToolsClass::ch2arr($str1), ToolsClass::ch2arr($str2));
            if(count($intersect) > 4){
                return true;
            }
            return false;
        }
    }

    //获取团队签到数据(分页)
    public function getTeamData($team_type,$team_id){
        if(!$this->create_at){ $this->create_at = date('Y-m-d'); }
        $model = $team_type == 1 ? (new SignBusiness()) : (new SignMaintain());
        if($team_id == 'business' || $team_id == 'maintain'){
            $where = ['left(create_at,10)'=>$this->create_at];
        }else{
            $where = ['team_id'=>$this->team_id, 'left(create_at,10)'=>$this->create_at];
        }
        $signBuiness = $model->find()->where($where)->select('id,member_name,member_avatar,shop_name,shop_address,longitude,latitude,late_sign,create_at')->orderBy('id DESC');
        $pagination = new Pagination(['totalCount'=>$signBuiness->count(),'pageSize'=>10]);
        $pagination->validatePage = false;
        $data['sign_data'] = $signBuiness->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $data['total'] = $signBuiness->count();
        $data['not_sign'] = (SignTeamMember::find()->where(['team_id'=>$this->team_id])->count() - $signBuiness->groupBy('member_id')->count());
        foreach ($data['sign_data'] as $k => $v){
            $data['sign_data'][$k]['create_at'] = substr($v['create_at'],11,5);
        }
        return $data;
    }
    //获取团队签到数据(全部)
    public function getTeamAllData($team_type,$team_id){
        if(!$this->create_at){ $this->create_at = date('Y-m-d'); }
        if (Yii::$app->user->identity->sign_team_admin == 1 && $team_id == '') {
            $team_id = 'business';
        }
        $model = $team_type == 1 ? (new SignBusiness()) : (new SignMaintain());
        if($team_id == 'business'){
            $model = new SignBusiness();
            $where = ['team_type' => 1,'left(create_at,10)'=>$this->create_at];
        }elseif ($team_id == 'maintain'){
            $model = new SignMaintain();
            $where = ['team_type' => 2, 'left(create_at,10)'=>$this->create_at];
        }else{
            $where = ['team_id'=>$this->team_id, 'left(create_at,10)'=>$this->create_at];
        }
        $signBuiness = $model->find()->where($where)->select('id,shop_name,shop_address,longitude,latitude,late_sign,create_at')->orderBy('id DESC');
        $data['total'] = strval($signBuiness->count());
        $data['sign_data'] = $signBuiness->asArray()->all();
        return $data;
    }
    /*
    public function getSignData(){
        if(!$this->create_at){ $this->create_at = date('Y-m'); }
        $teamInfo = $this->getTableField('yl_sign_team_member','team_id',['member_id'=>$this->member_id]);
        $this->team_id = $teamInfo['team_id'];
        $teamType = SignTeam::getTeamType($this->team_id);
        $memberObj = Member::findOne($this->member_id);
        $model = $teamType == 1 ? (new SignBusiness()) : (new SignMaintain());
        $field = $teamType == 1 ? '' : ',evaluate';
        $data = $model->find()->where(['member_id'=>$this->member_id, 'left(create_at,7)'=>$this->create_at])->select(new Expression('id,member_id,member_name,member_avatar,late_sign,shop_name,shop_address,substr(create_at,1,10) as rq,substr(create_at,11,6) as tm'.$field))->orderBy('rq DESC,tm DESC');
        $pagination = new Pagination(['totalCount'=>$data->count(),'pageSize'=>10]);
        $pagination->validatePage = false;
        $re = $data->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $result = [];
        $result['member_name'] = $memberObj->getAttribute('name');
        $result['avatar'] = $memberObj->getAttribute('avatar');
        $tobj = SignTeam::findOne($this->team_id);
        $result['team_name'] = $tobj ? $tobj->getAttribute('team_name') : '';
        $result['total_num'] = $data->count();
        if(!empty($re)){
            $arr = array_merge(array_unique(array_column($re,'rq')));
            foreach ($arr as $k => $v){
                foreach ($re as $kk => $vv){
                    $result['data'][$k]['time'] = $v;
                    if($v == $vv['rq']){
                        $result['data'][$k]['items'][] = $vv;
                    }
                }
            }
        }
        return $result;

    }
    */

    //签到数据详情  1、超时签到人数 2、未达标人数 3、未签到人数 4、中评人数 5、差评人数
    public function getMembersView(){
        $where = ['create_at' => $this->create_at];
        $orderBy = '';
        $sort = ' DESC';
        $model = '';
        switch ($this->member_type){
            case 1:
                $orderBy = 'overtime_sign_member_number'.$sort;
                break;
            case 2:
                $orderBy = 'unqualified_member_number'.$sort;
                break;
            case 3:
                $orderBy = 'no_sign_member_number'.$sort;
                break;
            case 4:
                $orderBy = 'middle_evaluate_number'.$sort;
                break;
            case 5:
                $orderBy = 'bad_evaluate_number'.$sort;
                break;
        }
        $data = [];
        if($this->team_id == 'business'){
            $data = SignTeamBusinessCount::find()->where($where)->orderBy($orderBy)->asArray()->all();
        }elseif ($this->team_id == 'maintain'){
            $data = SignTeamMaintainCount::find()->where($where)->orderBy($orderBy)->asArray()->all();
        }else{
            $where['team_id'] = $this->team_id;
            $teamType = SignTeam::getTeamType($this->team_id);
            $model = $teamType == 1 ? (new SignTeamBusinessCount()) : (new SignTeamMaintainCount());
            $data = $model->find()->where($where)->orderBy($orderBy)->asArray()->all();
        }
        $arr = [];
        /*
        if(!empty($data)){
            foreach ($data as $k => $v){
                $arr[$k]['team_name'] = SignTeam::findOne($v['team_id'])->getAttribute('team_name');
                $arr[$k]['items'] = self::find()->where(['team_id'=>$v['team_id'], 'yl_sign_team_count_member_detail.member_type'=>$this->member_type])->select('member_id,team_type,team_id,`name`,avatar')->joinWith('memberInfo',false)->asArray()->all();
            }
        }
        */
        if(!empty($data)){
            foreach ($data as $k => $v){
                //团队名称
                $arr[$k]['team_name'] = SignTeam::findOne($v['team_id'])->getAttribute('team_name');
                //团队类型
                $teamType = SignTeam::getTeamType($v['team_id']);

                if($this->member_type == 1 && $teamType == 1){//业务签到超时  SignBusiness
                    $arr[$k]['items'] = Sign::find()->where(['team_id'=>$v['team_id'], 'late_sign'=>1,'left(create_at,10)' => $this->create_at])->select('id,team_id,member_id,team_member_type,member_name as name,member_avatar as avatar')->asArray()->all();
                }elseif ($this->member_type == 1 && $teamType == 2){//维护签到超时  SignMaintain
                    $arr[$k]['items'] = Sign::find()->where(['team_id'=>$v['team_id'], 'late_sign'=>1,'left(create_at,10)' => $this->create_at])->select('id,team_id,member_id,team_member_type,member_name as name,member_avatar as avatar')->asArray()->all();
                }elseif ($this->member_type == 2 && $teamType == 1){//业务未达标
                    $arr[$k]['items'] = SignMemberCount::find()->where(['team_id'=>$v['team_id'],'qualified'=>0,'yl_sign_member_count.create_at' => $this->create_at])->joinWith('memberInfo',false)->asArray()->all();
                }elseif ($this->member_type == 2 && $teamType == 2){//维护未达标
                    $arr[$k]['items'] = SignMemberCount::find()->where(['team_id'=>$v['team_id'],'qualified'=>0,'yl_sign_member_count.create_at' => $this->create_at])->joinWith('memberInfo',false)->asArray()->all();
                }elseif ($this->member_type == 3 && $teamType == 1){//业务未签到
                    $arr[$k]['items'] = SignMemberCount::find()->where(['team_id'=>$v['team_id'],'sign_number'=>0,'yl_sign_member_count.create_at' => $this->create_at])->joinWith('memberInfo',false)->asArray()->all();
                }elseif ($this->member_type == 3 && $teamType == 2){//维护未签到
                    $arr[$k]['items'] = SignMemberCount::find()->where(['team_id'=>$v['team_id'],'sign_number'=>0,'yl_sign_member_count.create_at' => $this->create_at])->select('name,avatar')->joinWith('memberInfo',false)->asArray()->all();
                }elseif ($this->member_type == 4 && $teamType == 2){//维护中评  SignMaintain
                    $arr[$k]['items'] = Sign::find()->where(['team_id'=>$v['team_id'], 'evaluate'=>2,'left(create_at,10)' => $this->create_at])->select('id,team_id,member_id,team_member_type,member_name as name,member_avatar as avatar')->asArray()->all();
                }elseif ($this->member_type == 5 && $teamType == 2){//维护差评  SignMaintain
                    $arr[$k]['items'] = Sign::find()->where(['team_id'=>$v['team_id'], 'evaluate'=>3,'left(create_at,10)' => $this->create_at])->select('id,team_id,member_id,team_member_type,member_name as name,member_avatar as avatar')->asArray()->all();
                }
                //1、超时签到人数 2、未达标人数 3、未签到人数 4、中评人数 5、差评人数
            }
        }
        return $arr;
    }

    //检查签到省市区
    public function checkAddressSearch(){
        //if(!$this->address_search){
         //  return false;
        //}
        $arr = explode(',',$this->address_search);
       // $nrr = array_filter($arr);
       // if(3 != count($nrr)){
         //   return false;
       // }
        $this->province = $arr[0];
        $this->city = $arr[1];
        $this->area = $arr[2];
        //$this->address_search = $nrr;
        return true;
    }

    /*
     * 判断重复店铺并设置mongoId
     * @param shop_name string 店铺名称
     * */
    public function setSignMongoId($shop_name,$shop_address) {
        if(empty($mongoShops = $this->queryShopInMongo($shop_name,date('Y-m-d')))){
            $signMongo = $this->addShopToMongoCollection($shop_name,$shop_address);
        }else{
            //疑似重复店铺判断店铺类型
            foreach ($mongoShops as $k => $v){
                if($v['shop_type'] !== $this->shop_type){
                    //若店铺类型不一样排除
                    unset($mongoShops[$k]);
                }
            }
            if(empty($mongoShops)){
                $signMongo = $this->addShopToMongoCollection($shop_name,$shop_address);
            }else{
                //店铺名字判重
                $re = $this->judgeRepeatShop($mongoShops, $shop_name);
                if($re){
                    $this->mongo_id = get_object_vars($re)['oid'];
                    return '';
                }else{
                    $signMongo = $this->addShopToMongoCollection($shop_name,$shop_address);
                }
            }
        }
        $this->mongo_id = get_object_vars($signMongo)['oid'];
        return $this->mongo_id;
    }
    //获取sign数据
    public function getSignData(){
        return $this->hasOne(Sign::className(),['id'=>'sign_id'])->select('yl_sign.id,team_id,team_name,member_name,member_avatar,shop_name,shop_address,yl_sign.create_at,member_id');
    }
    public function scenes(){
        return [
            'get-view' =>[
                'member_type' => [
                    'required'=>'1',
                    'result'=>'SIGN_MEMBER_TYPE_EMPTY'
                ],
                'team_type' => [

                ],
                'create_at' => [
                    'required'=>'1',
                    'result'=>'SIGN_CREATE_AT_EMPTY'
                ],
                'team_id' => [
                    'required'=>'1',
                    'result'=>'SIGN_TEAM_ID_EMPTY'
                ],
            ],
            'team-all-data' => [
                'create_at' => [],
                'team_id' => [
                    'required'=>'1',
                    'result'=>'SIGN_TEAM_ID_EMPTY'
                ],
            ],
            //业务足迹
            'team-sign-view' => [
                'create_at' => [],
                'team_id' => [
                    'required'=>'1',
                    'result'=>'SIGN_TEAM_ID_EMPTY'
                ],
            ],

            //业务签到
            'create' => [
                'shop_acreage' => [
                    'required'=>'1',
                    'result'=>'SIGN_SHOP_ACREAGE_EMPTY'
                ],
                'shop_mirror_number' => [
                    'required'=>'1',
                    'result'=>'SIGN_SHOP_MIRROR_EMPTY'
                ],
                'longitude' => [
                    'required'=>'1',
                    'result'=>'LONGITUDE_EMPTY'
                ],
                'latitude' => [
                    'required'=>'1',
                    'result'=>'LATITUDE_EMPTY'
                ],
                'contacts_mobile' => [],
                'shop_type' => [
                    'required'=>'1',
                    'result'=>'SIGN_SHOP_TYPE_EMPTY'
                ],
                'screen_brand_name' => [],
                'minimum_charge' => [
                    'required'=>'1',
                    'result'=>'MINIMUM_EMPTY'
                ],
                'address_search' => [
                    [
                        [
                            'required' => '1',
                            'result' => 'ADDRESS_SEARCH_EMPTY'
                        ],
                        [
                            'function' => 'this::checkAddressSearch',
                            'result' => 'ADDRESS_SEARCH_ERROR'
                        ],
                    ]
                ],
                'screen_number' => [],
                'description' => [],
            ],

        ];
    }

}
