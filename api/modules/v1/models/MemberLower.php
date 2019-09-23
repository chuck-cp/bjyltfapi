<?php

namespace api\modules\v1\models;

use Yii;
use yii\data\Pagination;

/**
 * 下级成员
 */
class MemberLower extends \api\core\ApiActiveRecord
{
    public $page;
    public $keyword;
    public static function tableName()
    {
        return '{{%member_lower}}';
    }
    /*
     * 获取我的上级
     * */
   public function getMemberParent(){
       if($parent_id = Yii::$app->user->identity->parent_id){
           return Member::find()->where(['id'=>$parent_id])->select('id,name,avatar')->asArray()->one();
       }
       return null;
   }

    /*
     * 验证权限
     * */
    public function getMemberLowerById($member_id,$lower_member_id){
        return self::find()->where(['or',['member_id'=>$member_id,'lower_member_id'=>$lower_member_id],['member_id'=>$lower_member_id,'lower_member_id'=>$member_id]])->select('level,parent_member_id')->asArray()->one();
    }
   /*
    * 获取我的下级
    * */
    public function getMemberLower(){
        $memberLower = self::find()->where(['member_id'=>Yii::$app->user->id])->andFilterWhere(['like','lower_member_name',$this->keyword])->select('parent_member_id,parent_member_name,lower_member_id,lower_member_name,level');
        if(!empty($this->level)){
            $memberLower->andWhere(['level'=>$this->level]);
        }
        $pagination = new Pagination(['totalCount'=>$memberLower->count()]);
        $pagination->validatePage = false;
        $memberLower = $memberLower->orderBy("level asc,id asc")->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        if(empty($memberLower)){
            return [];
        }
        $memberModel = new Member();
        foreach($memberLower as $key=>$lower){
            if($lowerModel = Member::find()->where(['id'=>$lower['lower_member_id']])->select('avatar')->asArray()->one()){
                $memberLower[$key]['lower_member_avatar'] = $lowerModel['avatar'];
            }else{
                $memberLower[$key]['lower_member_avatar'] = '';
            }
        }
        return $memberLower;
    }


    /*
     * 场景
     * */
    public function scenes()
    {
        return [
            'index' => [
                'page' => [
                    'type' => 'int',
                ],
                'keyword' => [
                ],
                'level'=>[
                    'type' => 'int',
                ]
            ]
        ];
    }
}
