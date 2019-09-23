<?php

namespace api\modules\v1\models;
use api\core\ApiModel;
use api\core\MongoActiveRecord;
use common\libs\Redis;
use MongoRegex;
use Yii;
use api\modules\v1\models\SignMemberCount;
use api\modules\v1\models\SignTeam;
use yii\base\Exception;
use yii\data\Pagination;
use common\libs\ToolsClass;
use yii\db\Expression;

/**
 * This is the model class for table "{{%sign_maintain}}".
 *
 * @property string $id
 * @property string $team_id
 * @property string $shop_id
 * @property string $member_id
 * @property string $shop_name
 * @property string $shop_address
 * @property string $longitude
 * @property string $latitude
 * @property string $contacts_name
 * @property string $contacts_mobile
 * @property integer $shop_type
 * @property string $maintain_content
 * @property string $screen_start_at
 * @property string $screen_end_at
 * @property string $description
 * @property integer $frist_sign
 * @property integer $late_sign
 * @property integer $evaluate
 * @property string $create_at
 */
class SignMaintain extends MongoActiveRecord
{
    public $word;
    public $address_search;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sign_maintain}}';
    }
    //判断时间是否合法
    public function beforeSave($insert){
        if(parent::beforeSave($insert)){
            if($insert){
                $today = date('Y-m-d');
                $differ = strtotime($today.' '.$this->screen_end_at) - strtotime($today.' '.$this->screen_start_at);
                if($differ > 0){
                    if(floor($differ/60) < 600){
                        return 'START_END_TIME_ERROR';
                    }
                }
            }
        }
        return true;
    }

    // 签到完成以后的操作
    public function afterCreateSign() {
        try {
            //如果开关机时间与店铺现在的开关机时间不一致，将写入redis待处理
            if($this->judgeStartShut($this->screen_start_at, $this->screen_end_at, $this->shop_id)){
                $shopData = [
                    'shop_id'=>$this->shop_id,
                    'screen_start_at' => $this->screen_start_at,
                    'screen_end_at' => $this->screen_end_at,
                ];
                Redis::getInstance(1)->lpush('system_push_data_to_device_list',json_encode(['type'=>'update', 'data'=>$shopData]));
                //修改店铺表里的开关机时间
                ShopApply::updateAll(['screen_start_at' => $this->screen_start_at,'screen_end_at' => $this->screen_end_at], ['id'=>$this->shop_id]);
            }
            $maintainData = [
                'type' => 'member',
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

    // 设置店铺数据
    public function setShopData($shop_id){
        $shopModel = $this->getTableField('yl_shop','shop_operate_type,area',['id'=>$shop_id]);
        if(!$shopModel){
            return false;
        }
        $this->shop_type = $shopModel['shop_operate_type'];
        $this->area_id = $shopModel['area'];
        return true;
    }

    //判断维护新提交的开关机时间是否与店铺现有开关机时间一致
    public function judgeStartShut($start_at, $end_at, $shop_id){
        if(!$start_at || !$end_at){
            return false;
        }
        $shopTm = $this->getTableField('yl_shop_apply','screen_start_at,screen_end_at', ['id'=>$shop_id]);
        if(empty($shopTm)){
            return true;
        }
        return ($start_at !== $shopTm['screen_start_at'] || $end_at !== $shopTm['screen_end_at']);
    }
    //获取待签到店铺列表
    public function getShops($jd,$wd){
        //读取配置
        $miter = $this->getTableField('yl_system_config','content',['id'=>'maintain_trimming_distance']);
        $distance = $miter ? floatval($miter['content']) : floatval(2000);
        $where = array(
            'loc' => array(
                '$near' => array(
                    '$geometry' => array(
                        'type' => 'Point',
                        'coordinates' => array(floatval($jd), floatval($wd)),
                    ),
                    '$maxDistance' => $distance,
                )
            )
        );
        $nearbyCircle = $this->mongoFindAll('shop',$where,[
            //'limit' => 0,
            //'select' => ['id','name','address'],
        ]);
        return $nearbyCircle;
    }
    //店铺搜索
    public function getSearchShops(){
        //读取配置
        $miter = $this->getTableField('yl_system_config','content',['id'=>'maintain_trimming_distance']);
        $distance = $miter ? floatval($miter['content']) : floatval(2000);
        $where = array(
            'name' => array(
                '$regex' => $this->word
            ),
            'loc' => array(
                '$near' => array(
                    '$geometry' => array(
                        'type' => 'Point',
                        'coordinates' => array(floatval($this->longitude), floatval($this->latitude)),
                    ),
                    '$maxDistance' => $distance,
                )
            )
        );
        $nearbyCircle = $this->mongoFindAll('shop',$where,[
            'limit' => 0,
            //'select' => ['id','name','address'],
        ]);
        return $nearbyCircle;
    }
    //获取团队足迹
    public function getFootmark(){
        $data = [];
        /****************************************************/
        if($this->team_id){
            if($this->team_id == 'maintain'){
                $where = ['left(create_at,10)'=>$this->create_at];
                $teamObj = 'all';
            }else{
                $where = ['team_id'=>$this->team_id,'left(create_at,10)'=>$this->create_at];
                $teamObj = SignTeam::findOne($this->team_id);
            }
        }else{
            $team = $this->getTableField('yl_sign_team_member','team_id',['member_id'=>\Yii::$app->user->id]);
            $team_id = isset($team['team_id']) ? $team['team_id'] : '0';
            $data['team_id'] = $team_id;
            $where = ['team_id'=>$team_id, 'left(create_at,10)'=>$this->create_at];
            $teamObj = SignTeam::findOne($team_id);
        }
        /****************************************************/
        if(!$this->create_at){ $this->create_at = ToolsClass::getDate(false,false); }
        $data['now_date'] = ToolsClass::getDate(true,false);
        if(!$teamObj){
            $data['team_name'] = '';
            $data['sign_data'] = [];
            $data['total'] = '0';
            $data['not_sign'] = '0';
            $data['team_id'] = '0';
        }else{
            if($teamObj == 'all'){
                $data['team_name'] = '全部维护组';
                $data['team_id'] = 'maintain';
                $where2 = [];
            }else{
                $data['team_name'] = $teamObj->team_name;
                $data['team_id'] = strval($teamObj->id);
                $where2 = ['team_id'=>$team_id];
            }
            $signBuiness = self::find()->where($where)->select('id,member_name,member_avatar,member_id,shop_name,team_id,shop_address,longitude,latitude,late_sign,create_at');
            $pagination = new Pagination(['totalCount'=>$signBuiness->count(),'pageSize'=>10]);
            $pagination->validatePage = false;
            $data['sign_data'] = $signBuiness->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
            foreach ($data['sign_data'] as $k => $v){
                $data['sign_data'][$k]['create_at'] = substr($v['create_at'],11,5);
            }
            $data['total'] = $signBuiness->count();
            $data['not_sign'] = strval((SignTeamMember::find()->where($where2)->count() - $signBuiness->groupBy('member_id')->count()));
        }
        return $data;

    }
    //维护内容
    public static function getMainTainContent(){
        return [
                    ['id'=>'1', 'content'=>'日常检查'],
                    ['id'=>'2', 'content'=>'屏幕检修'],
                    ['id'=>'3', 'content'=>'网络检查'],
                    ['id'=>'4', 'content'=>'拷贝内容'],
                    ['id'=>'5', 'content'=>'更新安装包'],
                    ['id'=>'6', 'content'=>'调整设备开关机时间']
               ];
    }
    //获取店铺最新维护信息
    public static function getNewComment($shop_id){
        $data = self::find()->where(['shop_id'=>$shop_id])->select('yl_sign_maintain.id,sign_id,member_name,yl_sign.create_at,maintain_content,screen_start_at,screen_end_at')->joinWith('sign',false)->orderBy('yl_sign.create_at DESC')->asArray()->one();
        if(!empty($data)){
            $arr = array_column(self::getMainTainContent(),'content','id');
            $contentArr = explode(',',$data['maintain_content']);
            end($contentArr);
            $last_key = key($contentArr);
            $data['content'] = '';
            foreach ($contentArr as $k => $v){
                if($k !== $last_key){
                    if($v == 6){
                        $data['content'] .= $arr[$v].':'.$data['screen_start_at'].'-'.$data['screen_end_at'].'、';
                    }else{
                        $data['content'] .= $arr[$v].'、';
                    }
                }else{
                    if($v == 6){
                        $data['content'] .= $arr[$v].':'.$data['screen_start_at'].'-'.$data['screen_end_at'];
                    }else{
                        $data['content'] .= $arr[$v];
                    }
                }

            }
        }
        return empty($data) ? null : $data;
    }
    //获取维护历史
    public function getMaintainHistory(){
        $obj = self::find()->where(['shop_id'=>$this->shop_id])->select(new Expression('yl_sign_maintain.id,sign_id,maintain_content,evaluate,member_name,substr(yl_sign.create_at,1,10) as rq,screen_start_at,screen_end_at'))->joinWith('sign',false);
        $pagination = new Pagination(['totalCount'=>$obj->count(), 'pageSize'=>10]);
        $pagination->validatePage = false;
        $data = $obj->offset($pagination->offset)->limit($pagination->limit)->orderBy('yl_sign.create_at DESC')->asArray()->all();
        if(!empty($data)){
            $crr = array_column(self::getMainTainContent(),'content','id');
            foreach ($data as $k => $v){
                $contentArr = explode(',',$v['maintain_content']);
                end($contentArr);
                $last_key = key($contentArr);
                $data[$k]['content'] = '';
                foreach ($contentArr as $key => $item) {
                    if($key !== $last_key){
                        if($item == 6){
                            $data[$k]['content'] .= $crr[$item].':'.$v['screen_start_at'].'-'.$v['screen_end_at'].'、';
                        }else{
                            $data[$k]['content'] .= $crr[$item].'、';
                        }
                    }else{
                        if($item == 6){
                            $data[$k]['content'] .= $crr[$item].':'.$v['screen_start_at'].'-'.$v['screen_end_at'];
                        }else{
                            $data[$k]['content'] .= $crr[$item];
                        }
                    }
                }
            }
        }
        return $data;
    }
    //关联sign签到表
    public function getSign(){
        return $this->hasOne(Sign::className(),['id'=>'sign_id'])->select('yl_sign.id,member_name');
    }
    //获取评价详情
    public static function getCommemt($id){
        $data = self::find()->where(['sign_id'=>$id])->select(new Expression('yl_sign_maintain.id,sign_id,shop_name,shop_address,member_name,maintain_content,evaluate,evaluate_description,yl_sign.create_at,screen_start_at,screen_end_at'))->joinWith('sign',false)->orderBy('evaluate_at DESC')->asArray()->one();
        if(!empty($data)){
            $arr = array_column(self::getMainTainContent(),'content','id');
            $contentArr = explode(',',$data['maintain_content']);
            //end($arr);
            //$last_key = key($arr);
            $data['content'] = '';
            foreach ($contentArr as $k => $v){
                if($v == 6){
                    $data['content'] .= $arr[$v].':'.$data['screen_start_at'].'-'.$data['screen_end_at'].'  ';
                }else{
                    $data['content'] .= $arr[$v].' ';
                }
            }
        }
        return $data;
    }
    //评价
    public function evaluate(){
        try{
            $model = self::find()->where(['sign_id'=>$this->id])->one();
            if($this->evaluate_description){
                $model->evaluate_description = $this->evaluate_description;
            }
            $redis = Redis::getInstance(1);
            $data = [
                'evaluate' => $this->evaluate,
                'oldEvaluate' => $model->evaluate,
                'create_at' => substr(Sign::findOne($model->sign_id)->getAttribute('create_at'),0,10),
                'team_id' => Sign::findOne($model->sign_id)->team_id,
            ];
            $redis->lpush('list_json_sign_evaluate_count',json_encode($data));
            $model->evaluate_at = date('Y-m-d H:i:s');
            $model->evaluate = $this->evaluate;
            $model->save();
            return 'SUCCESS';
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return 'ERROR';
        }
    }
    //检查签到省市区
    public function checkAddressSearch(){
        if(!$this->address_search){
            return false;
        }
        $arr = explode(',',$this->address_search);
        $nrr = array_filter($arr);
        if(3 != count($nrr)){
            return true;
        }
        $this->province = $nrr[0];
        $this->city = $nrr[1];
        $this->area = $nrr[2];
        return true;
    }

    public function getIsTeam(){
        //var_dump($this->team_id);exit;
        return $this->hasOne(Member::className(),['id'=>'member_id'])->select('id,name');
    }
    //默认评价时间
    public function updateEvaluateTime(){
        if(!Sign::find()->where(['member_id'=>Yii::$app->user->id,'id'=>$this->id])){
            return 'SIGN_DATA_NOT_EXIST';
        }
        try{
            $currentModel = self::find()->where(['sign_id'=>$this->id])->one();
            $currentModel->default_evaluate_at = date('Y-m-d',strtotime("+3 day"));
            $currentModel->save();
            return 'SUCCESS';
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return 'ERROR';
        }
    }
    public function scenes(){
        return [
            //评价
            'evaluate' => [
                  'evaluate' => [
                      'required'=>'1',
                      'result'=>'SIGN_EVALUATE_EMPTY'
                  ],
                    'id' => [
                        'required' => 1,
                        'result' => 'SIGN_ID_EMPTY',
                    ],
                  'evaluate_description' => [],
            ],
            //评价历史
            'maintain-history' => [
                'shop_id' => [
                    'required' => 1,
                    'result' => 'SIGN_SHOP_ID_EMPTY',
                ],
            ],
            //业务足迹
            'team-footmark' => [
                'create_at' => [],
                'team_id' => [
                    'required'=>'1',
                    'result'=>'SIGN_TEAM_ID_EMPTY'
                ],
            ],
            'create' => [
                'shop_id' => [
                    [
                        [
                            'required' => '1',
                            'result' => 'SIGN_SHOP_ID_EMPTY'
                        ],
                        [
                            'function' => 'this::setShopData',
                            'result' => 'SIGN_SHOP_TYPE_ERROR'
                        ],
                    ]
                ],
                'longitude' => [
                    'required'=>'1',
                    'result'=>'LONGITUDE_EMPTY'
                ],
                'latitude' => [
                    'required'=>'1',
                    'result'=>'LATITUDE_EMPTY'
                ],
                'contacts_name' => [
                    'required'=>'1',
                    'result'=>'SIGN_CONTACTS_NAME_EMPTY'
                ],
                'contacts_mobile' => [
                    'required'=>'1',
                    'result'=>'SIGN_CONTACTS_MOBILE_EMPTY'
                ],
                'maintain_content' => [
                    'required'=>'1',
                    'result'=>'SIGN_MAINTAIM_CONTENT_EMPTY'
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
                'screen_start_at' => [],
                'screen_end_at' => [],
                'description' => [],
            ],
            'shop-search' => [
                'longitude' => [
                    'required'=>'1',
                    'result'=>'LONGITUDE_EMPTY'
                ],
                'latitude' => [
                    'required'=>'1',
                    'result'=>'LATITUDE_EMPTY'
                ],
                'word' => [
                    'required'=>'1',
                    'result'=>'SIGN_WORD_EMPTY'
                ],
            ],
            'close-evaluate' => [
                'id' => [
                    'required' => 1,
                    'result' => 'SIGN_ID_EMPTY',
                ],
            ],

        ];
    }


}
