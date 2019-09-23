<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use Yii;
use yii\base\Exception;
use common\libs\Redis;
/**
 * This is the model class for table "{{%shop_advert_image}}".
 *
 * @property string $id
 * @property string $shop_id
 * @property string $image_url
 * @property string $image_size
 * @property string $image_sha
 * @property integer $status
 * @property integer $sort
 */
class ShopAdvertImage extends ApiActiveRecord
{
    //图片排序需要传入的参数
    public $sort_json;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop_advert_image}}';
    }

    public function beforeSave($insert){
        if(parent::beforeSave($insert)){
            if($insert){
                //店铺图片总数量不能超过30张
                $currentNum = ShopAdvertImage::find()->where(['shop_id'=>$this->shop_id])->count();
                if($currentNum > 29){
                    return 'IMAGE_MAX_THIRTY';
                }
                $this->image_url = str_replace('http://yulongchuanmei-1255626690.cossh.myqcloud.com','https://i1.bjyltf.com',$this->image_url);
            }else{
                if($this->shop_id){
                    $res = (new Shop())->checkAuthShop($this->shop_id,Yii::$app->user->id,'shop_member_id');
                    if($res !== 'SUCCESS'){
                        return $res;
                    }
                }
            }
            return true;
        }else{
            return false;
        }
    }
    /*
     * 获取某店铺的展示的图片
     */
    public function getMyImages($shop_id){
        return self::find()->where(['shop_id'=>$shop_id, 'shop_type'=>$this->shop_type])->select('id,shop_id,image_url,status,sort')->orderBy('sort DESC,id ASC')->asArray()->all();
    }
    /*
     * 店铺发布编辑后的图片
     */
    public function releaseImages($shop_id){
        $re = self::updateAll(['status'=>1],['shop_id'=>$shop_id,'shop_type'=>$this->shop_type]);
        if($re !== false){
            $redis  = Redis::getInstance(5);
            if($this->shop_type == 2) {
                $pushData = ['head_id'=>$shop_id, 'shop_id'=>0];
            }else{
                $pushData = ['head_id'=>0, 'shop_id'=>$shop_id];
            }
            $redis->lpush('push_shop_custom_advert_list',json_encode($pushData));
            return 'SUCCESS';
        }
        return 'ERROR';
    }
    /*
     * 店铺图片删除或排序
     */
    public function sortMyIamges($shop_id){
        try{
            $redis  = Redis::getInstance(5);
            if($this->shop_type == 2) {
                $pushData = ['head_id'=>$shop_id, 'shop_id'=>0];
            }else{
                $pushData = ['head_id'=>0, 'shop_id'=>$shop_id];
            }
            if(empty($this->sort_json)){
                //删除所有的已上传的图片
                self::deleteAll(['shop_id'=>$shop_id, 'shop_type'=>$this->shop_type]);
                $redis->lpush('push_shop_custom_advert_list',json_encode($pushData));
                return 'SUCCESS';
            }
            //最新图片集
            $images_arr = json_decode($this->sort_json,true);
            //原来的图片集
            $old_iamges = $this->getMyImages($shop_id);
            //删除
            $max = count($images_arr);
            if($max < count($old_iamges)){
                $del_arr = array_diff(array_column($old_iamges,'id'),array_column($images_arr,'id'));
                foreach ($del_arr as $v){
                    self::findOne($v)->delete();
                }
            }
            //排序
            foreach ($images_arr as $k => $v){
                $imageModel = self::find()->where(['id'=>$v['id'],'shop_type'=>$this->shop_type])->one();
                if(!$imageModel){
                    return 'ERROR';
                }
                $imageModel->sort = $max - $k;
                $imageModel->save();
            }
            $redis->lpush('push_shop_custom_advert_list',json_encode($pushData));
            return 'SUCCESS';
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return 'ERROR';
        }

    }

    public function checkShopId(){
        if(!Shop::find()->where(['id'=>$this->shop_id,'shop_member_id'=>Yii::$app->user->id])->one()){
            return false;
        }
        return true;
    }
    /*
     * 场景  IMAGE_SHOP_ID_EMPTY
     * */
    public function scenes(){
        return [
            'save-shop-image'=>[
//                'shop_id' => [
//                    'required' => '1',
//                    'result' => 'IMAGE_SHOP_ID_EMPTY',
//                ],
                'shop_id' => [
                    'function' => 'this::checkShopId',
                    'result' => 'IMAGE_SHOP_ID_EMPTY'
                ],
                'image_url'=>[
                    'required' => '1',
                    'result' => 'IMAGE_URL_EMPTY',
                ],
                'shop_type' => [
                    'required' => '1',
                    'result' => 'SHOP_TYPE_EMPTY',
                ],
                'image_size' => [
                    'required' => '1',
                    'result' => 'IMAGE_SIZE_EMPTY',
                ],
                'image_sha' => [
                    'required' => '1',
                    'result' => 'IMAGE_SHA_EMPTY',
                ],
            ],
            'shop-image-show'=>[
//                'shop_id' => [
//                    'required' => '1',
//                    'result' => 'IMAGE_SHOP_ID_EMPTY',
//                ],
                'shop_type' => [
                    'required' => '1',
                    'result' => 'SHOP_TYPE_EMPTY',
                ],
            ],
            'release'=>[
                'shop_type' => [
                    'required' => '1',
                    'result' => 'SHOP_TYPE_EMPTY',
                ],
            ],
            'image-sort'=>[
                'sort_json' => [],
                'shop_type' => [
                    'required' => '1',
                    'result' => 'SHOP_TYPE_EMPTY',
                ],
            ],
        ];
    }
}
