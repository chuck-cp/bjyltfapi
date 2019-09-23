<?php

namespace api\modules\v1\models;

use Yii;

/*
 * 系统模块
 * */
class SystemFunction extends \api\core\ApiActiveRecord
{

    public static function tableName()
    {
        return '{{%system_function}}';
    }
    /*
     * 根据用户类型获取菜单状态
     * */
    public static function getFunctionStatus(){
        $status = 1;
        //如果不是内部人员但是他有上级也可以看到内部安装模块(此模块已改成1)
        //$parent = (new self())->getTableField('yl_member','parent_id',['id'=>Yii::$app->user->id]);
        //只有团队负责人或者管理员能看 团队管理 和 签到数据 模块
        $memberType = SignTeamMember::getMemberType(Yii::$app->user->id);
        //$memberType = 0;
        if(Yii::$app->user->identity->inside == 1 && in_array($memberType,[2,3])){
            $status = [1,3,4];
        }elseif (Yii::$app->user->identity->inside !== 1){
            $status = [1];
        }elseif (Yii::$app->user->identity->inside == 1 && !in_array($memberType,[2,3])){
            $status = [1,3];
        }
        return $status;
    }
    /*
     * 获取系统模块
     * */
    public function getSystemFunction(){

        return self::find()->select('id,name,image_url,target,link_url')->where(['status'=>SystemFunction::getFunctionStatus()])->asArray()->all();
    }
}
