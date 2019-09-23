<?php

namespace wap\models;

use Yii;
use yii\data\Pagination;

class MemberWeixin extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%member_weixin}}';
    }
    /*
     * 获取openid 对应的数据id
     * */
    public function getOpenid($id){
        $openid=self::find()->where(['id'=>$id])->select('id,member_id')->asArray()->one();
        if(empty($openid)){
            return false;
        }
        return $openid;
    }
    /*
   *  保存openid
   * */
    public function saveOpenid($openid){
        $openid_id = self::find()->where(['open_id'=>$openid])->select('id')->asArray()->one();
        if(empty($openid_id)){
            $this->open_id = $openid;
            $this->save();
            return $this->id;
        }
        return $openid_id['id'];
    }
}
