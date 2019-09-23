<?php

namespace pc\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * member表的model
 */
class MemberCopyright extends ActiveRecord
{
    public $order_id;
    /**
     * 表名
     */
    public static function tableName()
    {
        return '{{%member_copyright}}';
    }

    public static function findIdentity($id){
        return static::findOne(['id' => $id]);
    }


    public function getId(){
        return $this->getPrimaryKey();
    }
    
    public function delAll($where){
        $res = self::deleteAll(['in', 'id', $where]);
        return $res;
    }

    public function getall($uid){
        return self::find()->where(['member_id'=>$uid])->select('name,image_url')->asArray()->all();
    }
    /*
     * action Modifyname
     * 保存上传的产权
     */
    public function saveCopyright($data){
        if($data){
//            $strlen=strlen($data['url']);       //全部字符长度
//            $tp=strpos($data['url'],"?"); //265之前的字符长度
//            $data['url']=substr($data['url'],-$strlen,$tp);  //从头开始截取到指字符位置。
            $copyright=new self;
            $copyright->member_id=$data['memberid'];
            $copyright->image_url=$data['url'];
            $copyright->name=$data['name'];
            if($copyright->save()){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
