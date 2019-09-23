<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use api\core\MongoActiveRecord;
use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "{{%shop_screen_advert_maintain}}".
 *
 * @property string $id
 * @property string $mongo_id
 * @property string $shop_id
 * @property string $apply_name
 * @property string $apply_mobile
 * @property string $shop_name
 * @property string $shop_image
 * @property string $shop_area_id
 * @property string $shop_area_name
 * @property string $shop_address
 * @property string $create_user_id
 * @property string $create_user_name
 * @property integer $status
 * @property string $install_member_id
 * @property string $install_member_name
 * @property string $install_finish_at
 * @property string $create_at
 * @property string $assign_at
 * @property string $assign_time
 * @property string $problem_description
 */
class ShopScreenAdvertMaintain extends MongoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop_screen_advert_maintain}}';
    }

    /**
     * 获取维护列表
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getAdvertMaintainList()
    {
        return self::find()->where(['and',['=','status','1'],['install_member_id'=>Yii::$app->user->id]])->select("id,mongo_id,shop_id,shop_image,shop_name,shop_area_name,shop_address,status,install_member_id,screen_number,assign_time as tm,assign_at,screen_number")->asArray()->all();
    }


    /**
     * 根据Id获取维护详情
     * @return array
     */
    public function getMainInfoByID(){
        $mainInfo = self::find()->where(['id'=>$this->id,'shop_id'=>$this->shop_id])->select('shop_name, apply_name, apply_mobile, shop_area_name, shop_address')->asArray()->one();
        $hzrInfo = Shop::find()->where(['id'=>$this->shop_id])->select('member_name, member_mobile')->asArray()->one();
        return array_merge($mainInfo, $hzrInfo);
    }

    public function getMongoByID(){
        $mainInfo = self::find()->where(['id'=>$this->id,'mongo_id'=>$this->mongo_id])->select('id, images, problem_description')->asArray()->one();
        if(!$mainInfo){
            return [];
        }
        $mongoInfo = $this->mongoFindOne('order_arrival_report', ['where'=>['_id'=>$this->mongo_id]]);
        if(!empty($mongoInfo)){
            $tp = 0;
            if($mainInfo['images']){
                $imageInfo = json_decode($mainInfo['images'],true);
                if(is_array($imageInfo)){
                    $tp = 1;
                }
            }
            foreach ($mongoInfo['software_number'] as $k => $v){
                $mongoInfo['software_number'][$k]['device_number'] = SystemDevice::getDevice($v['number']);
                if($tp){
                    foreach ($imageInfo as $kk => $vv){
                        if($vv['number'] == $v['number'] && $vv['pic']){
                            $mongoInfo['software_number'][$k]['pic'] = $vv['pic'];
                        }else{
                            $mongoInfo['software_number'][$k]['pic'] = '';
                        }
                    }
                }
            }
        }
        $mongoInfo['problem_description'] = $mainInfo['problem_description'];
        return $mongoInfo;
    }

    /**
     * 临时保存
     * @return bool
     */
    public function saveTempInfo(){
        try{
            $currentModel = self::find()->where(['id'=>$this->id])->one();
            if(!$currentModel){ return false; }
            $currentModel->images = json_encode($this->images);
            $currentModel->problem_description = $this->problem_description;
            $currentModel->save();
            return true;
        }catch (Exception $e){
            \Yii::error($e->getMessage(),'error');
            return false;
        }
    }

    /**
     * 保存
     * @return bool
     */
    public function saveInfo(){
        $mongoInfo = $this->getMongoByID();
        $flag = false;
        if(!empty($mongoInfo['software_number'])){
            foreach ($mongoInfo['software_number'] as $k => $v){
                if(!$v['date']){
                    $flag = true;
                    break;
                }
            }
        }
        if($flag){ return $mongoInfo; }
        $dbTrans = Yii::$app->db->beginTransaction();
        try{
            //广告维护
            $currentModel = self::find()->where(['id'=>$this->id, 'status'=>1])->one();
            if(!$currentModel){ return false; }
            $currentModel->images = str_replace('\\','',trim(json_encode($this->images),'"'));
            $currentModel->problem_description = $this->problem_description ? $this->problem_description : ' ';
            $currentModel->status = 2;
            $currentModel->save();
            //安装历史
            $historyModel = new MemberInstallHistory();
            $historyModel->shop_id = $currentModel->shop_id;
            $historyModel->shop_name = $currentModel->shop_name;
            $historyModel->replace_id = $this->id;
            $historyModel->area_name = $currentModel->shop_area_name;
            $historyModel->address = $currentModel->shop_address;
            $historyModel->screen_number = $currentModel->screen_number;
            $historyModel->type = 5;
            $historyModel->create_at = date('Y-m-d');
            $historyModel->shop_image = $currentModel->shop_image;
            $historyModel->member_id = Yii::$app->user->id;
            $historyModel->save();
            $dbTrans->commit();
            return true;
        }catch (Exception $e){
            \Yii::error($e->getMessage(),'error');
            $dbTrans->rollBack();
            return false;
        }
    }
    public function scenes()
    {
        return [
            'get-maintain-info' => [
                'shop_id' => [
                    'required' => 1,
                    'result' => 'SHOP_ID_EMPTY',
                ],
                'id' => [
                    'required' => 1,
                    'result' => 'ID_EMPTY',
                ],
            ],
            'get-mongo-info' => [
                'mongo_id' => [
                    'required' => 1,
                    'result' => 'MONGO_ID_EMPTY',
                ],
                'id' => [
                    'required' => 1,
                    'result' => 'ID_EMPTY',
                ],
                'images' => [],
                'problem_description' => [],
            ],
            'save-info' => [
                'mongo_id' => [
                    'required' => 1,
                    'result' => 'MONGO_ID_EMPTY',
                ],
                'id' => [
                    'required' => 1,
                    'result' => 'ID_EMPTY',
                ],
                'images' => [
                    'required' => 1,
                    'result' => 'IMAGES_EMPTY',
                ],
                'problem_description' => [],
            ],
            'save-temp-info' => [
                'id' => [
                    'required' => 1,
                    'result' => 'ID_EMPTY',
                ],
                'images' => [],
                'problem_description' => [],
            ],
        ];
    }

}
