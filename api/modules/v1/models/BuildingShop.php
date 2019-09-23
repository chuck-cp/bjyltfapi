<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use api\core\ApiModel;
use common\libs\ArrayClass;
use common\libs\ToolsClass;
use Yii;

class BuildingShop
{
    const SHOP_FLOOR          = 1;  // 安装场景-楼宇
    const SHOP_PARK           = 2;  // 安装场景-公园
    const SCREEN_LED          = 1;  // 设备类型-LED
    const SCREEN_POSTER       = 2;  // 设备类型-画报
    const EXAMINE_STATUS_0   = 0;  // 申请待审核
    const EXAMINE_STATUS_1   = 1;  // 申请未通过
    const EXAMINE_STATUS_2   = 2;  // 待安装
    const EXAMINE_STATUS_3   = 3;  // 安装待审核
    const EXAMINE_STATUS_4   = 4;  // 安装未通过
    const EXAMINE_STATUS_5   = 5;  // 已安装
    const EXAMINE_STATUS_6   = 6;  // 已关闭
    protected $shopModel;
    protected $shopData;
    protected $shopType;
    public function __construct($shop_type,$where,$resultType = 'one',$filed = '*')
    {
        if ($this->shopType = $shop_type) {
            if ($shop_type == self::SHOP_FLOOR) {
                $this->shopModel = new BuildingShopFloor();
            } else {
                $this->shopModel = new BuildingShopPark();
            }
            if ($resultType == 'many') {
                $this->shopData = $this->shopModel->find()->select($filed)->where($where)->asArray()->all();
            } elseif ($resultType == 'object') {
                $this->shopModel = $this->shopModel->findOne($where);
            } else {
                $this->shopData = $this->shopModel->find()->select($filed)->where($where)->asArray()->one();
            }
        }
    }


    // 数组中如果不保护某个KEY,就value写入该数组的KEY中
    public function supplementArray($key,$value,&$array)
    {
        if (!isset($array[$key])) {
            $array[$key] = $value;
        }
    }

    /*
     * 获取安装业务首页数据
     * 返回店铺表、楼宇表、公园表的数据集合
     * */
    public function getShopList()
    {
        $result = [];
        $modelList = ['ShopHeadquarters','Shop','BuildingShopFloor','BuildingShopPark'];
        foreach ($modelList as $modelKey => $model) {
            $model = 'api\modules\v1\models\\'.$model;
            $shopModel = (new $model())->getInstallShopList();
            if (empty($shopModel)) {
                continue;
            }
            $shop_type = $modelKey - 1;
            foreach ($shopModel as $key => &$value) {
                // 总部数据字段不全，需要做补齐操作
                $this->supplementArray('shop_image','',$value);
                $this->supplementArray('province','',$value);
                $this->supplementArray('city','',$value);
                $this->supplementArray('area','',$value);

                $value['shop_type'] = (string)$shop_type;
                $value['examine_status'] = ToolsClass::getCommonStatus('shopExamineStatus',$value['examine_status']);
                if (!isset($value['poster_screen_number'])) {
                    $value['screen_number'] = $value['led_screen_number'] ?? "0";
                    $value['screen_type'] = "1";
                    unset($value['led_screen_number']);
                } else {
                    $value['screen_number'] = $value['poster_screen_number'] ?? "0";
                    $value['screen_type'] = "2";
                    unset($value['poster_screen_number']);
                }
                $value['sort_create_at'] = strtotime($value['create_at']);
                $value['create_at'] = date('Y-m-d',$value['sort_create_at']);
                $value['recommend'] = "0";
                if (isset($value['activity_detail_id'])) {
                    $value['recommend'] = $value['activity_detail_id'] > 0 ? "1": "0";
                    unset($value['activity_detail_id']);

                }
                $result[] = $value;
            }
        }
        return ArrayClass::sort($result,'sort_create_at','desc');
    }

    /*
     * 获取Model
     * */
    public function getShopModel() : ApiActiveRecord {
        return $this->shopModel;
    }

    /*
     * 获取公司信息
     * */
    public function getCompanyData()
    {
        $companyModel = new BuildingCompany();
        return $companyModel->getCompanyData($this->company_id,'company_name');
    }

    /*
     * 提交申请审核
     * @param screen_type int 设备类型
     * */
    public function submitExamine($screen_type)
    {
        try {
            if ($screen_type == self::SCREEN_LED) {
                $this->shopModel::updateAll(['led_examine_status' => 0],['id' => $this->id, 'led_examine_status' => 1]);
            } else {
                $this->shopModel::updateAll(['poster_examine_status' => 0],['id' => $this->id, 'poster_examine_status' => 1]);
            }
            return true;
        } catch (\Throwable $e) {
            Yii::error($e->getMessage());
            return false;
        }
    }

    /*
     * 提交安装审核
     * @param screen_type int 设备类型
     * */
    public function installSubmitExamine($screen_type)
    {
        try {
            if ($screen_type == self::SCREEN_LED) {
                if ($this->led_total_screen_number != $this->led_screen_number) {
                    return false;
                }
                return $this->shopModel::updateAll(['led_examine_status' => 3],['id' => $this->id, 'led_examine_status' => 2, 'led_install_member_id' => Yii::$app->user->id]);
            } else {
                if ($this->poster_total_screen_number != $this->poster_screen_number) {
                    return false;
                }
                return $this->shopModel::updateAll(['poster_examine_status' => 3],['id' => $this->id, 'poster_examine_status' => 2, 'poster_install_member_id' => Yii::$app->user->id]);
            }
        } catch (\Throwable $e) {
            Yii::error($e->getMessage());
            return false;
        }
    }

    /*
     * 获取场景店铺信息
     * */
    public function getShopDetail()
    {
        $screen_type = (int)Yii::$app->request->get('screen_type');
        $examine_status = '';
        $examine_result = '';
        if ($screen_type == self::SCREEN_LED) {
            $examine_status = $this->led_examine_status;
            $examine_result = LogExamine::getExamineResult($this->id,$this->getExamineKey($screen_type));
        } elseif ($screen_type == self::SCREEN_POSTER) {
            $examine_status = $this->poster_examine_status;
            $examine_result = LogExamine::getExamineResult($this->id,$this->getExamineKey($screen_type));
        }

        $other_image = empty($this->other_image) ? null : explode(",",$this->other_image);
        if ($this->shopType == self::SHOP_FLOOR) {
            return [
                'company_id' => $this->company_id,
                'company_name' => $this->getCompanyData()['company_name'],
                'contact_name' => $this->contact_name,
                'contact_mobile' => $this->contact_mobile,
                'shop_name' => $this->shop_name,
                'floor_type' => $this->floor_type,
                'floor_number' => $this->floor_number,
                'low_floor_number' => $this->low_floor_number,
                'shop_level' => $this->shop_level,
                'shop_image' => $this->shop_image,
                'plan_image' => $this->plan_image,
                'floor_image' => $this->floor_image,
                'other_image' => $other_image,
                'area_id' => $this->area_id,
                'province' => $this->province,
                'city' => $this->city,
                'area' => $this->area,
                'address' => $this->address,
                'street' => $this->street,
                'description' => $this->description,
                'examine_status' => $examine_status,
                'examine_result' => $examine_result,
            ];
        } else {
            return [
                'position_id' => BuildingShopPosition::getPositionId($this->id,self::SHOP_PARK,self::SCREEN_POSTER),
                'company_id' => $this->company_id,
                'company_name' => $this->getCompanyData()['company_name'],
                'contact_name' => $this->contact_name,
                'contact_mobile' => $this->contact_mobile,
                'shop_name' => $this->shop_name,
                'shop_level' => $this->shop_level,
                'shop_image' => $this->shop_image,
                'plan_image' => $this->plan_image,
                'floor_number' => $this->floor_number,
                'other_image' => $other_image,
                'area_id' => $this->area_id,
                'province' => $this->province,
                'city' => $this->city,
                'area' => $this->area,
                'address' => $this->address,
                'street' => $this->street,
                'description' => $this->description,
                'examine_status' => $examine_status,
                'examine_result' => $examine_result,
            ];
        }
    }

    /*
     * 判断数据是否为空
     * */
    public function isEmpty()
    {
        return empty($this->shopData);
    }

    /*
     * 判断是否有权限访问
     * */
    public function isAuth()
    {
        if ($this->member_id == Yii::$app->user->id) {
            return true;
        }
        if ($this->shopType == self::SHOP_FLOOR) {
            if ($this->poster_install_member_id == Yii::$app->user->id || $this->led_install_member_id == Yii::$app->user->id) {
                return true;
            }
        } else {
            if ($this->poster_install_member_id == Yii::$app->user->id) {
                return true;
            }
        }
        return false;
    }

    /*
     * 获取审核状态
     * @screen_type int 设备类型
     * */
    public function getExamineStatus($screen_type)
    {
        if ($screen_type == self::SCREEN_LED) {
            return $this->led_examine_status;
        } else {
            return $this->poster_examine_status;
        }
    }

    /*
     * 获取审核记录表中的KEY
     * @screen_type int 设备类型
     * */
    public function getExamineKey($screen_type)
    {
        $resultStatus = $this->getExamineStatus($screen_type);
        if ($screen_type = self::SCREEN_LED) {
            if ($resultStatus == self::EXAMINE_STATUS_1) {
                return LogExamine::EXAMINE_KEY_10;
            } elseif ($resultStatus == self::EXAMINE_STATUS_4) {
                return LogExamine::EXAMINE_KEY_13;
            }
        } else {
            if ($this->shopType == self::SHOP_FLOOR) {
                if ($resultStatus == self::EXAMINE_STATUS_1) {
                    return LogExamine::EXAMINE_KEY_11;
                } elseif ($resultStatus == self::EXAMINE_STATUS_4) {
                    return LogExamine::EXAMINE_KEY_14;
                }
            } else {
                if ($resultStatus == self::EXAMINE_STATUS_1) {
                    return LogExamine::EXAMINE_KEY_12;
                } elseif ($resultStatus == self::EXAMINE_STATUS_4) {
                    return LogExamine::EXAMINE_KEY_15;
                }
            }
        }
    }

    public function __get($name)
    {
        if (!isset($this->shopData[$name])) {
            throw new \Exception("{$name} 字段不存在");
        }
        return $this->shopData[$name];
    }

}
