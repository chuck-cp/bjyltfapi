<?php

namespace api\modules\v1\models;

use MongoDB\Driver\Exception\AuthenticationException;
use Yii;
use yii\db\Exception;

class BuildingShopPositionDifferent extends \api\core\ApiActiveRecord
{

    public $screen_data;
    public static function tableName()
    {
        return '{{%building_shop_position_different}}';
    }


    /*
     * 获取所有楼层
     * @param shop_position_id int 位置ID
     * */
    public function getFloorNumber($shop_position_id)
    {
        $floorNumberData = self::find()->where(['and',['shop_position_id' => $shop_position_id],['>','floor_number',0]])->groupBy('floor_number')->select('floor_number')->asArray()->all();
        if (empty($floorNumberData)) {
            return [];
        }
        $floor_number = array_column($floorNumberData,'floor_number');
        sort($floor_number);
        return $floor_number;
    }


    /*
     * 设备入库
     * @param shop_id int 场景店铺ID
     * @param shop_position_id string 位置ID
     * @param floor_number int 楼层
     * */
    public function updateScreenData($shop_id,$shop_position_id)
    {
        try {
            $screenModel = new BuildingShopScreen();
            $screen_data = json_decode($this->screen_data,true);
            foreach ($screen_data as $dataKey => $dataValue) {
                $positionModel = self::find()->where(['id' => $dataValue['position_id'], 'shop_position_id' => $shop_position_id, 'floor_number' => $dataValue['floor_number']])->select('id,position_config_id,update_position_config_number')->asArray()->one();
                if (empty($positionModel)) {
                    return ['PARAM_ERROR',''];
                }
                $position_config_id = explode(",",$positionModel['position_config_id']);
                $update_position_config_number = explode(",",$positionModel['update_position_config_number']);
                foreach ($position_config_id as $key => $value) {
                    unset($position_config_id[$key]);
                    $position_config_id[$value] = $update_position_config_number[$key];
                }
                foreach ($dataValue['screen_list'] as $key => $value) {
                    if (empty($value['device_number']) || empty($value['image_url']) || empty($value['position_config_id']) || empty($value['position_name'])) {
                        continue;
                    }
                    $value['id'] = (int)$value['id'];
                    // 验证数量
                    if ($position_config_id[$value['position_config_id']] <= 0) {
                        continue;
                    }
                    $position_config_id[$value['position_config_id']]--;
                    // 验证设备
                    if (!BuildingShopScreen::verifyScreenNumber($value['device_number'])) {
                        return ['DEVICE_NUMBER_ERROR',$value['device_number']];
                    }
                    // 设备入库
                    if ($value['id'] > 0) {
                        // 更新图片
                        $update = ['image_url' => $value['image_url']];
                        if (isset($value['token']) && $value['token'] == md5($value['id'].$value['position_config_id'].Yii::$app->params['systemSalt'])) {
                            // 更换故障屏幕
                            $update['device_number'] = $value['device_number'];
                        }
                        BuildingShopScreen::updateAll($update,['id' => $value['id'],'position_different_id' => $positionModel['id'], 'position_config_id' => $value['position_config_id']]);
                    } else {
                        $cloneModel = clone $screenModel;
                        $cloneModel->shop_id = $shop_id;
                        $cloneModel->shop_position_id = $shop_position_id;
                        $cloneModel->position_different_id = $positionModel['id'];
                        $cloneModel->position_config_id = $value['position_config_id'];
                        $cloneModel->position_name = $value['position_name'];
                        $cloneModel->image_url = $value['image_url'];
                        $cloneModel->device_number = $value['device_number'];
                        $cloneModel->save();
                    }
                }
            }
            return ['SUCCESS',''];
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            return ['DEVICE_NUMBER_ERROR2',$value['device_number']];
        } catch (\Throwable $e) {
            Yii::error($e->getMessage());
            return ['ERROR',''];
        }

    }


    /*
     * 生成安装页面的设备列表
     */
    public function generateInstallScreenData(int $id,string $position_config_id,string $position_config_number,$screen_status = null)
    {
        $resultData = [];
        $position_config_id = explode(",",$position_config_id);
        $position_config_number = explode(",",$position_config_number);
        $screenData = BuildingShopScreen::getScreenDataOnInstallDetail($id,$position_config_id,$screen_status);
        foreach ($position_config_id as $idKey => $idValue) {
            for ($i = 0; $i < $position_config_number[$idKey]; $i++) {
                if (isset($screenData[$idValue]) && isset($screenData[$idValue][$i])) {
                    $resultData[] = $screenData[$idValue][$i];
                } else {
                    if ($screen_status != null) {
                        continue;
                    }
                    $resultData[] = [
                        'id' => '',
                        'position_config_id' => $idValue,
                        'device_number' => '',
                        'position_name' => BuildingPositionConfig::getPositionNameById($idValue),
                        'image_url' => ''
                    ];
                }
            }
        }
        return $resultData;
    }

    /*
     * 获取设备安装详情页的数据
     * @param shop_position_id string 位置ID
     * @param floor_number int 楼层
     * @param screen_status int 设备状态
     * */
    public function getInstallViewDetailData($shop_position_id,$floor_number,$screen_status = null)
    {
        $shopPositionModel = BuildingShopPosition::findOne($shop_position_id);
        if (!$shopPositionModel->isAuth()) {
            throw new AuthenticationException();
        }
        if (empty($floor_number)) {
            $floor_number = $shopPositionModel->getIndexFloorNumber();
        }
        $resultData = [];
        if ($floor_number == 'all') {
            $where = ['shop_position_id' => $shop_position_id];
            $resultData['floor_number'] = [];
        } else {
            $resultData['floor_number'] = $this->getFloorNumber($shop_position_id);
            if (empty($floor_number)) {
                // 当楼层为空时获取所有楼层并默认返回最低一层的数据
                $floor_number = $resultData['floor_number'][0] ?? 0;
            }
            $where = ['shop_position_id' => $shop_position_id,'floor_number' => $floor_number];
        }
        $positionData = self::find()->where($where)->asArray()->all();
        if (empty($positionData)) {
            return [];
        }
        $updatePositionConfigId = [];
        $updateDescription = [];
        foreach ($positionData as $key => $value) {
            $resultData['screen_data'][] = [
                'position_id' => $value['id'],
                'floor_number' => $value['floor_number'],
                'position_name' => $value['position_name'],
                'screen_list' => $this->generateInstallScreenData($value['id'],$value['position_config_id'],$value['update_position_config_number'],$screen_status)
            ];
            if ($value['update_position_config_id']) {
                $updatePositionConfigId = array_merge($updatePositionConfigId,explode(",",$value['update_position_config_id']));
            }
            if ($value['update_description']) {
                $updateDescription[] = $value['update_description'];
            }
        }
        $updatePositionConfigId = array_unique($updatePositionConfigId);
        $resultData['floor_data'] = $shopPositionModel->getPositionDetailOnInstall($positionData[0]['view_position_id'],$updatePositionConfigId);
        $resultData['update_description'] = $updateDescription;
        $resultData['index_floor_number'] = $floor_number;
        return $resultData;
    }

    public function scenes(){
        return [
            'update'=>[
                'screen_data'=>[
                    'required'=>'1',
                    'result'=>'SCREEN_DATA_EMPTY'
                ]
            ],
        ];
    }
}
