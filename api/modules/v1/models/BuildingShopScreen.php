<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "{{%building_shop_screen}}".
 *
 * @property string $id
 * @property string $shop_id
 * @property string $position_id
 * @property string $image_url
 * @property string $device_number
 */
class BuildingShopScreen extends \api\core\ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%building_shop_screen}}';
    }

    /*
     * 安装页面获取设备信息
     * @param position_different_id int 大位置ID
     * @param position_config_id array 小位置ID
     * */
    public static function getScreenDataOnInstallDetail(int $position_different_id,array $position_config_id,$screen_status = null)
    {
        $where = ['position_different_id' => $position_different_id,'position_config_id' => $position_config_id];
        if ($screen_status != null) {
            $where['status'] = (int)$screen_status;
        }
        $screenModel = BuildingShopScreen::find()->where($where)->asArray()->all();
        if (empty($screenModel)) {
            return [];
        }
        $resultData = [];
        foreach ($screenModel as $key => $value) {
            $data = [
                'id' => $value['id'],
                'position_config_id' => $value['position_config_id'],
                'image_url' => $value['image_url'],
                'position_name' => $value['position_name'],
                'device_number' => $value['device_number'],
            ];
            if ($screen_status == 0) {
                $data['token'] = md5($value['id'].$value['position_config_id'].Yii::$app->params['systemSalt']);
            }
            $resultData[$value['position_config_id']][] = $data;
        }
        return $resultData;
    }

    /*
     * 检查设备是否已经安装
     * */
    public static function isInstall($device_number)
    {
        return BuildingShopScreen::find()->where(['device_number' => $device_number])->count();
    }
    /*
     * 验证设备是否符合入库条件
     * @param device_number string 设备编号
     * */
    public static function verifyScreenNumber($device_number)
    {
        return SystemDeviceFrame::find()->where(['device_number' => $device_number, 'is_output' => 1, 'status' => 1, 'is_delete' => 1])->count();
    }

    /*
     * 获取未激活的设备编号
     * @param position_different_id int 位置ID
     * */
    public function getNoActivationScreen($position_different_id)
    {
        $screenModel = self::find()->where(['position_different_id' => $position_different_id,'status' => 0])->select('device_number')->asArray()->all();
        if (empty($screenModel)) {
            return [];
        }
        return array_column($screenModel,'device_number');
    }

    /*
     * 检查是否有未激活的设备
     * */
    public static function checkNoActivationScreen($shop_position_id)
    {
        $screenModel = BuildingShopScreen::find()->where(['shop_position_id' => $shop_position_id, 'status' => 0])->count();
        if ($screenModel) {
            return "1";
        }
        return "0";
    }
}
