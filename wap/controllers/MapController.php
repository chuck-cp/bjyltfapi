<?php
namespace wap\controllers;
use common\libs\ToolsClass;
use pc\models\SystemAddress;
use wap\core\WapController;
use wap\models\Shop;
use wap\models\SystemConfig;


/**
 * 地图
 */
class MapController extends WapController
{

    public function init()
    {
        $this->layout = false;
        parent::init();
    }

    public function actionBaidu($action,$area_id){
        /*if(parent::beforeAction($action)){
            if($this->authentication() < 1){
                print_r('TOKEN ERROR');
                return false;
            }
            return true;
        }*/
        return $this->render('baidu');
    }

    public function actionBaidu2($action,$area_id,$areaname){
        if(parent::beforeAction($action)){
            if($this->authentication() < 1){
                print_r('TOKEN ERROR');
                return false;
            }
        }
        if($area_id){
            $areaMap=['left(yl_shop.area,'.strlen($area_id).')' => $area_id];
        }else{
            $areaMap=['left(yl_shop.area,'.strlen(101).')' => 101];
        }
        $InstallFinish = Shop::find()->where(['and',['status'=>5],['not like','name','测试'],['<>','longitude',''],['<>','latitude',''],$areaMap])->select('id,name,area_name,address,screen_number,bd_longitude,bd_latitude,install_finish_at,status,mirror_account')->asArray()->all();
        $markerArr = [];
        foreach ($InstallFinish as $key1=>$value1){
            $markerArr[$key1]['content'] = "<b>店铺ID:</b> ".$value1['id']." </br><b>店铺名:</b> ".$value1['name']."</br><b>地区:</b> ".$value1['area_name']." </br><b>详细地址:</b> ".$value1['address']." </br><b>屏幕数量°:</b> ".$value1['screen_number']."</br><b>镜面数量:</b>".$value1['mirror_account'];
            $markerArr[$key1]['j'] = $value1['bd_longitude'];
            $markerArr[$key1]['w'] = $value1['bd_latitude'];
            $markerArr[$key1]['name'] = $value1['address'];
        }

        //地图级别
        switch (strlen($area_id)){
            case '3':
                $level=5;
                break;
            case '5':
                $level=8;
                break;
            case '7':
                $level=10;
                break;
            case '9':
                $level=11;
                break;
            case '12':
                $level=14;
                break;
        }
        return $this->render('baidu2',[
            'areaname'=>$areaname,
            'level'=>$level,
            'area_id'=>$area_id,
            'citys' => json_encode($markerArr),
        ]);
    }

    public function actionGaode($action,$area_id,$areaname){
        if(parent::beforeAction($action)){
            if($this->authentication() < 1){
                print_r('TOKEN ERROR');
                return false;
            }
        }

        //根据地区获取经纬度，地址解析
        $url='http://restapi.amap.com/v3/geocode/geo?key=a8f38c01f3380ccb2595751466073a16&address='.$areaname.'&city=';
        $aa=file_get_contents($url);
        $bb=json_decode($aa,true);
        if($area_id==101){
            $location='[116.397477,39.908692]';
        }else{
            $location='['.$bb['geocodes'][0]['location'].']';
        }

        if($area_id){
            $areaMap=['left(yl_shop.area,'.strlen($area_id).')' => $area_id];
        }else{
            $areaMap=['left(yl_shop.area,'.strlen(101).')' => 101];
        }
        $InstallFinish = Shop::find()->where(['and',['status'=>5],['not like','name','测试'],['<>','longitude',''],['<>','latitude',''],$areaMap])->select('id,name,area_name,address,screen_number,longitude,latitude,install_finish_at,status,mirror_account')->asArray()->all();
        $markerArr = [];
        foreach ($InstallFinish as $key1=>$value1){
            $markerArr[$key1]['title'] ='';
            $markerArr[$key1]['name'] = "<b>店铺编号:</b> ".$value1['id']."<div class='guanbi' onclick='feng()'><img src='/static/images/gaode/cha.png'></div> </br><b>店名:</b> ".$value1['name']."</br><b>地区:</b> ".$value1['area_name']." </br><b>详址:</b> ".$value1['address']." </br><b>安装台数:</b> ".$value1['screen_number']."</br><b>镜面数量:</b>".$value1['mirror_account'];
            $markerArr[$key1]['lnglat'][] = $value1['longitude'];
            $markerArr[$key1]['lnglat'][] = $value1['latitude'];
            $markerArr[$key1]['name2'] = $value1['address'];
            $markerArr[$key1]['style'] = 0;
        }
//地图级别
        switch (strlen($area_id)){
            case '3':
                $level=5;
                break;
            case '5':
                $level=9;
                break;
            case '7':
                $level=11;
                break;
            case '9':
                $level=13;
                break;
            case '12':
                $level=15;
                break;
        }
        return $this->render('gaode',[
            'level'=>$level,
            'location'=>$location,
            'area_id'=>$area_id,
            'citys' => json_encode($markerArr),
        ]);
    }
}
