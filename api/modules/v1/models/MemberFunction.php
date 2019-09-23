<?php

namespace api\modules\v1\models;

use Yii;


class MemberFunction extends \api\core\ApiActiveRecord
{

    public static function tableName()
    {
        return '{{%member_function}}';
    }
    /*
     * 获取系统首页的模块
     * */
    public function getIndexFunction(){
        $functionModel = self::find()->select('function_content')->where(['member_id'=>Yii::$app->user->id])->asArray()->one();
        if(empty($functionModel) || empty($functionModel['function_content'])){
            $result = SystemFunction::find()->where(['status'=>SystemFunction::getFunctionStatus()])->orderBy('id desc')->asArray()->all();
        }else{
            $function_id = explode(",",$functionModel['function_content']);
            $resultFunction = SystemFunction::find()->where(['id'=>$function_id,'status'=>SystemFunction::getFunctionStatus()])->asArray()->all();
            if(!empty($resultFunction)){
                foreach($resultFunction as $key=>$function){
                    $reformFunction[$function['id']] = $function;
                }
                foreach($function_id as $id){
                    if(!isset($reformFunction[$id])){
                        continue;
                    }
                    $result[] = $reformFunction[$id];
                }
            }
        }
        //我的店铺跳转判断
        $shopModel = new Shop();
        $my_shop = $shopModel->getMyShop();
        foreach ($result as $k => $v){
            if($v['name'] == '我的店铺'){
                $result[$k]['shop_id'] = $my_shop['shop_id'];
                $result[$k]['target'] = $my_shop['shop_type'];
            }
            //签到数据里加入当前用户的team_id,member_type,team_type
            if($v['name'] == '签到数据'){
                $result[$k]['team_id'] = strval(SignTeamMember::getTeamId(Yii::$app->user->id));
                $result[$k]['sign_member_type'] = strval(SignTeamMember::getMemberType(Yii::$app->user->id));
                $result[$k]['sign_team_type'] = strval(SignTeamMember::getTeamType());
            }
        }

        /********20190506新加是否是兼职业务人员*************/
        if(Yii::$app->user->identity->part_time_business){
            $funs = SystemFunction::find()->where(['or',['target'=>'guanggao'],['target'=>'advert_select']])->andWhere(['<>','status',2])->asArray()->all();
            $funs[] = [
                'name'=>'编辑',
                'image_url'=>'http://i1.bjyltf.com/function/bianji.png',
                'target'=>'bianji'
            ];
            return array_unique(array_merge($result,$funs),SORT_REGULAR);
        }
        $result[] = [
            'name'=>'编辑',
            'image_url'=>'http://i1.bjyltf.com/function/bianji.png',
            'target'=>'bianji'
        ];
        /************************************************/
        return $result;
    }

    /*
     * 获取我的系统模块
     * */
    public function getMemberFunction(){
        $functionModel = self::find()->select('function_content')->where(['member_id'=>Yii::$app->user->id])->asArray()->one();
        if(empty($functionModel)){
            return [];
        }
        return $functionModel['function_content'];
    }

    /*
     * 检查我是否设置过模块
     * */
    public function checkMyFunction($member_id){
        return MemberFunction::find()->where(['member_id'=>$member_id])->count();
    }

    /*
     * 加载默认模块
     * */
    public function loadDefaultFunction($member_id){
        $memberModel = Member::find()->where(['id'=>$member_id])->select('inside')->asArray()->one();
        if(empty($memberModel)){
            return false;
        }
        $systemAddress = SystemFunction::find()->where(['status'=>[1,3]])->select('id')->asArray()->all();
        $function_content = '';
        if(!empty($systemAddress)){
            $function_content = implode(",",array_column($systemAddress,'id'));
        }
        $this->member_id = $member_id;
        $this->function_content = $function_content;
    }
    /*
     * 修改模块
     * */
    public function updateContent(){
        $function_content = json_decode($this->function_content,true);
        $this->function_content = implode(",",array_column($function_content,'id'));
        if($functionModel = self::find()->where(['member_id'=>Yii::$app->user->id])->one()){
            $functionModel->function_content = $this->function_content;
            return $functionModel->save();
        }
        $this->member_id = Yii::$app->user->id;
        return $this->save();
    }
    /*
     * 场景
     * */
    public function scenes(){
        return [
            'update'=>[
                'function_content'=>[
                    'required'=>'1',
                    'result'=>'FUNCTION_CONTENT_EMPTY'
                ]
            ],
        ];
    }
}
