<?php

namespace pc\models;

use Yii;

class OrderCopyright extends \yii\db\ActiveRecord
{
	public static function tableName()
    {
        return '{{%order_copyright}}';
    }
	//根据订单获取知识产权图片
    public function getimg($order)
    {
        //var_dump($order);die;
        $this->order_id = $order;
        $img = self::find()->where(['order_id'=>$this->order_id])->select('image_url,name')->asArray()->all();
        return $img; 
    }
    //插入订单知识产权
    public function incopy($order_id,$copy)
    {
        $copModel = new self;
        $copModel->image_url = $copy['image_url'];
        $copModel->name = $copy['name'];
        $copModel->order_id =$order_id;
        if($copModel->save()){
            return true;
        }else{
            return false;
        }
    }
    /*
    * 批量插入订单知识产权
    * */
    public function allincopy($order_id,$copyright){
        $copyrightData=json_decode($copyright);
        if(!empty($copyrightData)){
            foreach($copyrightData as $k=>$v){
                if($v->id==0){
                    $copModel = new self;
                    $copModel->order_id = $order_id;
                    $copModel->image_url = $v->url;
                    $copModel->name = $v->name;
                    $copModel->save();
                }
            }
            return true;
        }else{
            return false;
        }
    }
}