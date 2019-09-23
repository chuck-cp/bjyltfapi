<?php
// 用于和外部应用交互的队列处理类
namespace api\controllers;
use api\core\ApiController;
use common\libs\RedisClass;
use common\libs\ToolsClass;


class QueueController extends ApiController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        if(in_array($this->action->id,['order-reward'])){
            unset($behaviors['authenticator']);
        }
        return $behaviors;
    }

    // 订单返利队列
    public function actionOrderReward(){
        try {
            $post = file_get_contents("php://input");
            $post = substr($post,0,1) == "=" ? substr($post,1) : $post;
            if (empty($post)) {
                return $this->returnData('ERROR');
            }
            $post = json_decode($post,true);
            if (isset($post[0]) && is_array($post[0])) {
                foreach ($post as $key => $value) {
                    RedisClass::rpush('queue_order_reward_list',json_encode($value),1);
                }
            } else {
                RedisClass::rpush('queue_order_reward_list',json_encode($post),1);
            }
            return $this->returnData();
        } catch (\Exception $e){
            \Yii::error($e->getMessage());
            return $this->returnData('ERROR',$e->getMessage());
        }
    }
}
