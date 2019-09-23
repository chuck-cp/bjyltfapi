<?php
namespace wap\controllers;
use wap\core\WapController;
use wap\models\SystemVersion;
use yii\web\Controller;


/**
 * 下载页面
 */
class DownloadController extends WapController
{

    public function actionIndex(){
        $versionModel = new SystemVersion();
        $iosVersion = $versionModel->getVersionUrl(2);
        $androidVersion = $versionModel->getVersionUrl(1);
        return $this->render('index',[
            'ios'=>$iosVersion,
            'android'=>$androidVersion
        ]);
    }
}
