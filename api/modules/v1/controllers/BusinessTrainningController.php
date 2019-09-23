<?php

namespace api\modules\v1\controllers;

use api\core\ApiController;
use api\modules\v1\models\SystemConfig;
use api\modules\v1\models\SystemTrain;
class BusinessTrainningController extends ApiController
{
    // 获取培训资料列表
    public function actionIndex(){
        $trainMaterial = (new SystemTrain())->getMaterialList();
        return $this->returnData('SUCCESS',['top_img'=>SystemConfig::getConfig('cover_map'),'items'=>$trainMaterial]);
    }

}
