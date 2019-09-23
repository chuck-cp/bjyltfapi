<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use api\modules\v1\models\MemberInfo;
use Yii;
use yii\base\Exception;
use yii\db\Expression;
use yii\web\MethodNotAllowedHttpException;

/**
 * This is the model class for table "{{%member_team}}".
 *
 * @property integer $member_id
 * @property string $member_name
 * @property string $team_name
 * @property string $live_area_id
 * @property string $live_area_name
 * @property string $live_address
 * @property string $company_name
 * @property string $company_area_name
 * @property string $company_area_id
 * @property string $company_address
 * @property integer $install_shop_number
 * @property integer $not_install_shop_number
 * @property integer $not_assign_shop_number
 * @property string $create_at
 */
class MemberTeam extends ApiActiveRecord
{
    public $team_member_mobile;
    public $shop_install_status;
    public $member_id;
    public $team_id;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_team}}';
    }

    /*
     * 取消指派店铺
     * @param team_id int 团队ID
     * @param shop_id int 店铺ID
     * */
    public function cancelAssignShop($team_id,$shop_id){
        $dbTrans = Yii::$app->db->beginTransaction();
        try{
            $shop_id = explode(",",$shop_id);
            $shop_number = count($shop_id);
            $screen_number = Shop::find()->where(['in','id',$shop_id])->select('sum(screen_number) as number')->asArray()->one();
            $screen_number = $screen_number['number'];
            if(!self::updateAll(['not_assign_shop_number'=>new Expression("not_assign_shop_number + $shop_number"),'not_install_shop_number'=>new Expression("not_install_shop_number - $shop_number")],['id'=>$team_id,'team_member_id'=>Yii::$app->user->id,'status'=>1])){
                throw new Exception("取消指派失败,你没有权限取消指派");
            }
            $shopModel = Shop::find()->where(['id'=>$shop_id,'install_team_id'=>$team_id])->select('install_member_id')->asArray()->one();
            if(empty($shopModel)){
                throw new Exception("取消指派失败,店铺不存在");
            }
            MemberTeamList::updateAll(['wait_shop_number'=>new Expression("wait_shop_number - {$shop_number}"),'wait_screen_number'=>new Expression("wait_screen_number - {$screen_number}")],['member_id'=>$shopModel['install_member_id'],'status'=>1]);
            $resultShop = Shop::updateAll(['install_member_id'=>0,'install_member_name'=>'','install_assign_at'=>'0000-00-00'],['id'=>$shop_id,'status'=>2]);
            if(empty($resultShop)){
                throw new Exception("指派失败,请勿重复取消");
            }
            $dbTrans->commit();
            return 'SUCCESS';
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            $dbTrans->rollBack();
            return 'ERROR';
        }
    }

    /*
     * 指派店铺
     * @param team_id int 团队ID
     * @param shop_id int 店铺ID
     * */
    public function assignShop($team_id,$shop_id){
        $dbTrans = Yii::$app->db->beginTransaction();
        try{
            $shop_id = explode(",",$shop_id);
            $shop_number = count($shop_id);
            $screen_number = Shop::find()->where(['in','id',$shop_id])->select('sum(screen_number) as number')->asArray()->one();
            $screen_number = $screen_number['number'];
            if(!self::updateAll(['not_assign_shop_number'=>new Expression("not_assign_shop_number - $shop_number"),'not_install_shop_number'=>new Expression("not_install_shop_number + $shop_number")],['id'=>$team_id,'team_member_id'=>Yii::$app->user->id,'status'=>1])){
                throw new Exception("指派失败,你没有权限指派店铺");
            }
            //如果是队长指派给自己,判断一下队长现在是不是电工
            if($this->member_id == Yii::$app->user->id){
                if(!MemberInfo::find()->where(['member_id'=>Yii::$app->user->id,'electrician_examine_status'=>1])->count()){
                    throw new Exception("指派失败,该成员还不是电工");
                }
            }
            if(!MemberTeamList::updateAllCounters(['wait_shop_number'=>$shop_number,'wait_screen_number'=>$screen_number],['team_id'=>$team_id,'member_id'=>$this->member_id,'status'=>1])){
                throw new Exception("指派失败,该用户不是你的组员");
            }
            $install_member =Member::find()->where(['id'=>$this->member_id])->select('name,mobile')->asArray()->one();
            if(empty($install_member)){
                throw new Exception("指派失败,指派的用户不存在");
            }
           $resultShop = Shop::updateAll(['install_member_id'=>$this->member_id,'install_member_name'=>$install_member['name'],'install_mobile'=>$install_member['mobile'],'install_assign_at'=>date('Y-m-d')],['id'=>$shop_id,'install_team_id'=>$team_id,'status'=>2]);
            if(empty($resultShop)){
                throw new Exception("指派失败");
            }
            $dbTrans->commit();
            return 'SUCCESS';
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            $dbTrans->rollBack();
            return 'ERROR';
        }
    }

    /*
     * 获取店铺列表
     * */
    public function shopList($team_id){
        if($this->shop_install_status == 1) {
            //未指派店铺
            $result = MemberTeam::find()->where(['id' => $team_id, 'team_member_id' => Yii::$app->user->id, 'status' => 1])->select('not_install_shop_number,not_assign_shop_number')->asArray()->one();
            if (empty($result)) {
                return [];
            }
            $result['shop_list'] = Shop::find()->where(['install_team_id' => $team_id, 'status' => 2,'install_member_id'=>0])->select('id,name,area_name,address,screen_number,install_member_id,shop_image,install_member_name')->asArray()->all();
        }else if($this->shop_install_status == 2){
            //已指派店铺
            $result = MemberTeam::find()->where(['id'=>$team_id,'team_member_id'=>Yii::$app->user->id,'status'=>1])->select('not_install_shop_number,not_assign_shop_number')->asArray()->one();
            if(empty($result)){
                return [];
            }
            $result['shop_list'] = Shop::find()->where(['and',['install_team_id'=>$team_id],['status'=>2],['>','install_member_id',0]])->select('id,name,area_name,address,screen_number,install_member_id,shop_image,install_member_name')->asArray()->all();
        }else{
            //已安装店铺
            $result = MemberTeam::find()->where(['id'=>$team_id,'team_member_id'=>Yii::$app->user->id,'status'=>1])->select('install_shop_number,install_screen_number')->asArray()->one();
            if(empty($result)){
                return [];
            }
            $result['shop_list'] = Shop::find()->where(['install_team_id'=>$team_id,'status'=>[3,4,5]])->select(['name','area_name','address','screen_number','install_member_name','shop_image','install_finish_at'=>'date(install_finish_at)','status'])->asArray()->all();
        }
        return $result;
    }

    /*
     * 退出团队
     */
    public function exitTeam($team_id){
        try{
            $listModel = MemberTeamList::findOne(['team_id'=>$team_id,'member_id'=>Yii::$app->user->id,'status'=>1,'member_type'=>1]);
            if(empty($listModel)){
                return 'EXIT_TEAM_ERROR';
            }
            if($listModel['wait_shop_number'] > 0){
                return 'EXIT_TEAM_WAIT_SHOP_LT_ZERO';
            }
            self::updateAll(['team_member_number'=>new Expression("team_member_number - 1")],['id'=>$team_id]);
            MemberInfo::updateAll(['join_team_id'=>0],['member_id'=>Yii::$app->user->id]);
            $listModel->delete();
            return 'SUCCESS';
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return 'ERROR';
        }
    }

    /*
     * 加入团队
     * */
    public function joinTeam(){
        if(!$memberModel = Member::find()->where(['mobile'=>$this->team_member_mobile,'name'=>$this->team_member_name,'team'=>1])->select('id')->one()){
            return 'TEAM_LEADER_DATE_ERROR';
        }
        if(!$memberInfoModel = MemberInfo::find()->where(['member_id'=>Yii::$app->user->id,'examine_status'=>1,'electrician_examine_status'=>1])->select('member_id,name,wait_screen_number')->one()){
            return 'MEMBER_NOT_CERTIFICATION';
        }
        if($memberInfoModel['wait_screen_number'] > 0){
            return 'TEAM_JOIN_ERROR_WAIT_SCREEN_NUMBER_LT_ZERO';
        }
        if(MemberTeamList::find()->where(['member_id'=>Yii::$app->user->id,'status'=>1])->count()){
            return 'TEAM_JOIN_REPEAT';
        }
        $teamModel = MemberTeam::find()->where(['team_member_id'=>$memberModel['id'],'status'=>1])->select('id')->asArray()->one();
        if(empty($teamModel)){
            return 'TEAM_DISSOLUTION';
        }
        $dbTrans = Yii::$app->db->beginTransaction();
        try{
            $listModel = new MemberTeamList();
            $listModel->member_id = Yii::$app->user->id;
            $listModel->member_name = $memberInfoModel['name'];
            $listModel->team_id = $teamModel['id'];
            $listModel->save();
            MemberTeam::updateAllCounters(['team_member_number'=>1],['id'=>$teamModel['id']]);
            MemberInfo::updateAll(['join_team_id'=>$teamModel['id']],['member_id'=>Yii::$app->user->id]);
            $dbTrans->commit();
            return 'SUCCESS';
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            $dbTrans->rollBack();
            return 'ERROR';
        }
    }
    /*
    * 创建团队
    * */
    public function createTeam(){
        if(MemberTeamList::find()->where(['member_id'=>Yii::$app->user->id,'status'=>1])->count()){
            return 'TEAM_CREATE_ERROR_ALREADY_JOIN_TEAM';
        }
//        if(self::find()->where(['and',['team_member_id'=>Yii::$app->user->id],['status'=>1]])->select('team_member_id')->one()){
//             return 'TEAM_ALREADY_EXISTED';
//        }
        if(self::find()->where(['and',['team_name'=>$this->team_name],['status'=>1]])->select('team_member_id')->one()){
            return 'TEAM_NAME_ALREADY_EXISTED';
        }
        $dbTrans = Yii::$app->db->beginTransaction();
        try {

            $this->team_member_id = Yii::$app->user->id;
            $this->create_at=date('Y-m-d H:i:s');
            $this->live_area_name = SystemAddress::getAreaNameById($this->live_area_id);
            $this->save();
            $teamListModel = new MemberTeamList();
            $teamListModel->team_id = $this->id;
            $teamListModel->member_id = Yii::$app->user->id;
            $teamListModel->member_name = Yii::$app->user->identity->name;
            $teamListModel->member_type = 2;
            $teamListModel->save();
            Member::updateAll(['team'=>1],['id'=>Yii::$app->user->id]);
            MemberInfo::updateAll(['join_team_id'=>$this->id],['member_id'=>Yii::$app->user->id]);
            $dbTrans->commit();
            return $this->id;
        } catch (Exception $e) {
            Yii::error("[createTeam]" . $e->getMessage());
            $dbTrans->rollBack();
            return false;
        }
    }
    /*
  * 获取团队信息
  * */
    public function getTeam($id){
        $teamInfo=self::find()->where(['id'=>$id])->select('id,team_member_id,team_member_name,team_name,live_area_id,live_area_name,live_address,company_name,company_area_name,company_area_id,company_address,status')->asArray()->one();
        if(empty($teamInfo)){
            return 'TEAM_NON_EXISTENT';
        }
        $memberinfo=Member::find()->where(['id'=>$teamInfo['team_member_id']])->select('id,name,mobile')->asArray()->one();
        $teamInfo['team_member_name']=$memberinfo['name'];
        $teamInfo['team_member_mobile']=$memberinfo['mobile'];
        return $teamInfo;
    }
    /*
     * 获取我的团队ID
     * */
    public static function getTeamId(){
        $teamModel = MemberTeam::find()->where(['team_member_id'=>Yii::$app->user->id,'status'=>1])->select('id')->asArray()->one();
        if(!empty($teamModel)){
            return $teamModel['id'];
        }
        return "0";
    }
    /*
 * 修改团队信息
 * */
    public function updateTeam(){
        try{
            if(self::find()->where(['and',['team_name'=>$this->team_name],['<>','id',$this->id]])->select('team_name')->one()){
                return 'TEAM_NAME_ALREADY_EXISTED';
            }
            $this->save();
            return 'SUCCESS';
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return 'ERROR';
        }
    }
    /*
*验证团队名称是否存在
* */
    public function isTeamName($team_name){
            if(self::find()->where(['and',['team_name'=>$team_name],['status'=>1]])->select('team_name')->one()){
                return 'ERROR';//团队名称已存在
            }else{
                return 'SUCCESS';//团队名称可以使用
            }
    }
    /*
  *解散团队
  * */
    public function dissolutionTeam(){
        $teamData=self::find()->where(['and',['id'=>$this->team_id],['status'=>1]])->select('id, not_install_shop_number,not_assign_shop_number,status')->one();
        if(empty($teamData)){
            return 'TEAM_ALREADY_DISSOLVE'; //团队已经解散
        }
        if($teamData['not_assign_shop_number']>0 or $teamData['not_install_shop_number']>0)
        {
            return 'TEAM_NOT_COMPLETE_TASK';//存在未完成的任务
        }
        $dbTrans = Yii::$app->db->beginTransaction();
        try {
            self::updateAll(['status' =>2],['id' => $this->team_id]);
            Member::updateAll(['team'=>0],['id'=>Yii::$app->user->id]);
            MemberTeamList::updateAll(['status' =>2],['team_id' => $this->team_id]);
            MemberInfo::updateAll(['join_team_id'=>0],['join_team_id'=>$this->team_id]);
            $dbTrans->commit();
            return 'SUCCESS';
        } catch (Exception $e) {
            Yii::error("[createTeam]" . $e->getMessage(),'db');
            $dbTrans->rollBack();
            return 'ERROR';
        }
    }

    /**
     * @param $replace_id
     */
    public static function getInstallInfo($replace_id,$install_id = 0){
        if($install_id == 0){
            $install = ShopScreenReplace::find()->where(['id'=>$replace_id])->select('install_member_id,install_member_name')->asArray()->one();
            if(empty($install)){
                return false;
            }
        }else{
            $install['install_member_id'] = $install_id;
        }

        $install['mobile'] = Member::findOne($install['install_member_id'])->mobile;
        $install['name'] = Member::findOne($install['install_member_id'])->name;
        //若存在安装人看是否属于团队
        $teamList = MemberTeamList::find()->where(['member_id'=>$install['install_member_id'],'status'=>1])->select('team_id,member_type')->asArray()->one();
        $team = [];
        if(!empty($teamList)){
            $team = MemberTeam::find()->where(['id'=>$teamList['team_id'],'status'=>1])->select('team_member_id,team_member_name')->asArray()->one();
            if(!empty($team)){
                $team['mobile'] = Member::findOne($team['team_member_id'])->mobile;
            }else{
                $team['mobile'] = '暂无手机号';
            }
        }
        return ['team'=>$team, 't_member'=>$install];
    }

    public function scenes()
    {
        return [
            'assign' => [
                'member_id' => [
                    'required' => '1',
                    'result'=>'MEMBER_ID_EMPTY',
                ],
            ],
            'shop' => [
                'shop_install_status' => [
                    'type'=>'int',
                    'default'=>'1'
                ],
            ],
            'join' => [
                'team_member_name' => [
                    'required' => '1',
                    'result'=>'MEMBER_NAME_EMPTY',
                ],
                'team_member_mobile'=> [
                    'required' => '1',
                    'result'=>'MEMBER_MOBILE_EMPTY',
                ]
            ],
            'create' => [
                'team_member_name'=> [
                    'required' => '1',
                    'result'=>'TEAM_MEMBER_NAME_EMPTY',
                ],
                'team_name'=> [
                    'required' => '1',
                    'result'=>'TEAM_NAME_EMPTY',
                ],
                'live_area_id'=> [
                    'required' => '1',
                    'result'=>'TEAM_AREA_ID_EMPTY',
                ],
                'live_address'=> [
                    'required' => '1',
                    'result'=>'TEAM_AREA_ADDRESS_EMPTY',
                ],
                'company_name'=> [
                ],
                'company_area_name'=> [
                ],
                'company_area_id'=> [
                ],
                'company_address'=> [
                ],
            ],
            'view' => [
                'team_member_id' => [
                ]
            ],
            'update' => [
                'team_name'=> [
                    'required' => '1',
                    'result'=>'TEAM_NAME_EMPTY',
                ],
                'live_area_id'=> [
                    'required' => '1',
                    'result'=>'TEAM_AREA_ID_EMPTY',
                ],
                'live_area_name'=> [
                    'required' => '1',
                    'result'=>'LIVE_AREA_NAME_EMPTY',
                ],
                'live_address'=> [
                    'required' => '1',
                    'result'=>'TEAM_AREA_ADDRESS_EMPTY',
                ],
                'company_name'=> [],
                'company_area_name'=> [],
                'company_area_id'=> [
                ],
                'company_address'=> [],
            ],
            'isteamname' => [
                'team_name'=> [
                    'required' => '1',
                    'result'=>'TEAM_NAME_EMPTY',
                ],
            ],
            'dismiss' => [
                'team_id'=> [
                    'required' => '1',
                    'result'=>'TEAM_ID_EMPTY',
                ],
            ]

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
