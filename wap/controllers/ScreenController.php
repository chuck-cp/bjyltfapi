<?php
namespace wap\controllers;
use wap\core\WapController;
use yii\web\Controller;


/**
 * 安装反馈详细信息
 */
class ScreenController extends WapController
{

    /**
     * 安装单验证动态码
     */
    public function actionLogin(){
        $dev = \Yii::$app->request->get('dev') ?? '';
        $token = \Yii::$app->request->get('token') ?? '';
        return $this->render('login',[
            'dev' => $dev,
            'token' => $token,
        ]);
    }

    /**
     * 安装单核对
     */
    public function actionCheck(){
        $dev = \Yii::$app->request->get('dev') ?? '';
        $token = \Yii::$app->request->get('token') ?? '';
        return $this->render('check',[
            'id'=>$_GET['number'],
            'apply_code'=>$_GET['apply_code'],
            'dynamic_code'=>$_GET['dynamic_code'],
            'dev' => $dev,
            'token' => $token,
        ]);
    }

    /**
     * 安装单确认安装提交
     */
    public function actionConfirm(){
        $dev = \Yii::$app->request->get('dev') ?? '';
        $token = \Yii::$app->request->get('token') ?? '';
        return $this->render('confirm',[
            'id'=>$_GET['number'],
            'apply_code'=>$_GET['apply_code'],
            'dynamic_code'=>$_GET['dynamic_code'],
            'dev' => $dev,
            'token' => $token,
        ]);
    }
    /**
     * 安装单确认等待激活
     */
    public function actionWait(){
        $token = \Yii::$app->request->get('token') ?? '';
        $change = \Yii::$app->request->get('change') ?? '';
        return $this->render('wait',[
            'id'=>$_GET['number'],
            'apply_code'=>$_GET['apply_code'],
            'dynamic_code'=>$_GET['dynamic_code'],
            'token' => $token,
            'change' => $change,
        ]);
    }
    /**
     * 安装单确认等待激活
     */
    public function actionOver(){
        $token = \Yii::$app->request->get('token') ?? '';
        return $this->render('over',[
            'id'=>$_GET['number'],
            'apply_code'=>$_GET['apply_code'],
            'dynamic_code'=>$_GET['dynamic_code'],
            'istrue'=>$_GET['istrue'],
            'token' => $token,
        ]);
    }
    /**
     * 安装单更新安装图片
     */
    public function actionUpdateimg(){
        $dev = \Yii::$app->request->get('dev') ?? '';
        return $this->render('updateimg',[
            'id'=>$_GET['number'],
            'apply_code'=>$_GET['apply_code'],
            'dynamic_code'=>$_GET['dynamic_code'],
            'token'=>$_GET['token'],
            'dev' => $dev,
        ]);
    }
    /**
     * 线下安装上传信息
     */
    public function actionUnderline(){
        $dev = \Yii::$app->request->get('dev') ?? '';
        return $this->render('underline',[
            'shopid'=>$_GET['shopid'],
            'token'=>$_GET['token'],
            'dev' => $dev,
        ]);
    }
    /**
     * 线下安装确认等待激活
     */
    public function actionUnderlinewait(){
        return $this->render('underlinewait',[
            'shopid'=>$_GET['shopid'],
            'token'=>$_GET['token'],
        ]);
    }
    /**
     * 线下安装单确认激活结果
     */
    public function actionUnderlineover(){
        return $this->render('underlineover',[
            'shopid'=>$_GET['shopid'],
            'token'=>$_GET['token'],
            'istrue'=>$_GET['istrue'],
        ]);
    }
    /**
     * 安装单核对
     */
    public function actionUnderlinecheck(){
        $dev = \Yii::$app->request->get('dev') ?? '';
        return $this->render('underlinecheck',[
            'shopid'=>$_GET['shopid'],
            'token'=>$_GET['token'],
            'dev' => $dev,
        ]);
    }
    /**
     * 线下安装上传信息
     */
    public function actionUnderlineupdate(){
        return $this->render('underlineupdate',[
            'shopid'=>$_GET['shopid'],
            'token'=>$_GET['token'],
            'isupdate'=>$_GET['isupdate'],
        ]);
    }
    /**
     * 线下安装更新安装图片
     */
    public function actionUnderlineupdateimg(){
        $dev = \Yii::$app->request->get('dev') ?? '';
        return $this->render('underlineupdateimg',[
            'shopid'=>$_GET['shopid'],
            'token'=>$_GET['token'],
            'dev' => $dev,
        ]);
    }
    /**
     * 屏幕管理 屏幕安装列表页
     */
    public function actionScreenshoplist(){
        $dev = \Yii::$app->request->get('dev') ?? '';
        $member_id = \Yii::$app->request->get('member_id');
        $token = \Yii::$app->request->get('token');
        return $this->render('/new-screen/list',[
            'member_id'=>$member_id,
            'token'=>$token,
            'dev' => $dev,
        ]);
    }
    /**
     * 屏幕管理 店铺信息核对
     */
    public function actionScreencheck(){
        $shopid = \Yii::$app->request->get('shopid');
        $token = \Yii::$app->request->get('token');
        $change = \Yii::$app->request->get('change') ?? '';
        $dev = \Yii::$app->request->get('dev');
        $status = \Yii::$app->request->get('status');
        //return $this->render('screencheck',[
        return $this->render('/new-screen/screencheck',[
            'shopid'=>$shopid,
            'token'=>$token,
            'dev' => $dev,
            'change' => $change,
            'status' => $status,
            'replace_id' => \Yii::$app->request->get('replace_id'),
            'operate' => \Yii::$app->request->get('operate'),
            'status' => \Yii::$app->request->get('status'),
        ]);
    }
    /**
     * 拆除屏幕时的check
     */
    public function actionRemoveCheck(){
        $shopid = \Yii::$app->request->get('shopid');
        $token = \Yii::$app->request->get('token');
        $change = \Yii::$app->request->get('change') ?? '';
        $dev = \Yii::$app->request->get('dev');
        //return $this->render('screencheck',[
        return $this->render('/remove-screen/remove-check',[
            'shopid'=>$shopid,
            'token'=>$token,
            'dev' => $dev,
            'change' => $change,
            'replace_id' => \Yii::$app->request->get('replace_id'),
            'operate' => \Yii::$app->request->get('operate'),
        ]);
    }
    /**
     * 拆除屏幕时的表单
     */
    public function actionRemoveView(){
        $dev = \Yii::$app->request->get('dev');
        $shopid = \Yii::$app->request->get('shopid');
        $token = \Yii::$app->request->get('token');
        return $this->render('/remove-screen/remove-view',[
            'shopid'=>$shopid,
            'token'=>$token,
            'dev' => $dev,
            'replace_id' => \Yii::$app->request->get('replace_id'),
            'operate' => \Yii::$app->request->get('operate'),
            'status' => \Yii::$app->request->get('status'),
        ]);
    }
    /*
     * 更换屏幕表单
     */
    public function actionChange(){
        $shopid = \Yii::$app->request->get('shopid');
        $token = \Yii::$app->request->get('token');
        $change = \Yii::$app->request->get('change') ?? '';
        $dev = \Yii::$app->request->get('dev');
        $replace_id = \Yii::$app->request->get('replace_id');
        $operate = \Yii::$app->request->get('operate');
        //return $this->render('change',[
        return $this->render('/change-screen/change',[
            'shopid'=>$shopid,
            'token'=>$token,
            'dev' => $dev,
            'change' => $change,
            'replace_id' => $replace_id,
            'operate' => $operate,
            'status' => \Yii::$app->request->get('status'),
        ]);
    }
    public function actionHd(){
        $shopid = \Yii::$app->request->get('shopid');
        $token = \Yii::$app->request->get('token');
        $change = \Yii::$app->request->get('change') ?? '';
        $dev = \Yii::$app->request->get('dev');
        $replace_id = \Yii::$app->request->get('replace_id');
        $operate = \Yii::$app->request->get('operate');
        $status = \Yii::$app->request->get('status');
        //return $this->render('change',[
        return $this->render('/change-screen/hd',[
            'shopid'=>$shopid,
            'token'=>$token,
            'dev' => $dev,
            'change' => $change,
            'replace_id' => $replace_id,
            'operate' => $operate,
            'status' => $status,
        ]);
    }
    public function actionNewHd(){
        $shopid = \Yii::$app->request->get('shopid');
        $token = \Yii::$app->request->get('token');
        $change = \Yii::$app->request->get('change') ?? '';
        $dev = \Yii::$app->request->get('dev');
        $replace_id = \Yii::$app->request->get('replace_id');
        $operate = \Yii::$app->request->get('operate');
        $status = \Yii::$app->request->get('status');
        //return $this->render('change',[
        return $this->render('/new-screen/new-hd',[
            'shopid'=>$shopid,
            'token'=>$token,
            'dev' => $dev,
            'change' => $change,
            'replace_id' => $replace_id,
            'operate' => $operate,
            'status' => $status,
        ]);
    }
    /**
     * 屏幕管理 安装上传信息
     */
    public function actionScreenconfirm(){
        $dev = \Yii::$app->request->get('dev');
        $shopid = \Yii::$app->request->get('shopid');
        $token = \Yii::$app->request->get('token');
        //return $this->render('screenconfirm',[
        return $this->render('/new-screen/screenconfirm',[
            'shopid'=>$shopid,
            'token'=>$token,
            'dev' => $dev,
            'replace_id' => \Yii::$app->request->get('replace_id'),
            'operate' => \Yii::$app->request->get('operate'),
        ]);
    }
    //更换屏幕页面
    public function actionChangeView(){
        $dev = \Yii::$app->request->get('dev');
        $shopid = \Yii::$app->request->get('shopid');
        $token = \Yii::$app->request->get('token');
        $replace_id = \Yii::$app->request->get('replace_id');
        //return $this->render('change-view',[
        return $this->render('/change-screen/change-view-new',[
            'shopid'=>$shopid,
            'token'=>$token,
            'dev' => $dev,
            'replace_id' => $replace_id,
            'member_id' => \Yii::$app->user->id,
        ]);
    }
    /**
     * 屏幕管理 等待激活
     */
    public function actionScreenwait(){
        $dev = \Yii::$app->request->get('dev');
        $shopid = \Yii::$app->request->get('shopid');
        $token = \Yii::$app->request->get('token');
        $change = \Yii::$app->request->get('change') ?? '';
        $replace_id = \Yii::$app->request->get('replace_id') ?? '';
        $operate = \Yii::$app->request->get('operate') ?? '';
        //return $this->render('screenwait',[
        return $this->render('/new-screen/screenwait',[
            'shopid'=>$shopid,
            'token'=>$token,
            'dev' => $dev,
            'change'=>$change,
            'replace_id' => $replace_id,
            'operate' => $operate,
        ]);
    }
    public function actionChangeScreenwait(){
        $dev = \Yii::$app->request->get('dev');
        $shopid = \Yii::$app->request->get('shopid');
        $token = \Yii::$app->request->get('token');
        $change = \Yii::$app->request->get('change') ?? '';
        $replace_id = \Yii::$app->request->get('replace_id') ?? '';
        //return $this->render('screenwait',[
        return $this->render('/change-screen/screenwait',[
            'shopid'=>$shopid,
            'token'=>$token,
            'dev' => $dev,
            'change'=>$change,
            'replace_id' => $replace_id,
        ]);
    }
    /**
     * 屏幕管理 结束处理
     */
    public function actionScreenover(){
        $dev = \Yii::$app->request->get('dev');
        $shopid = \Yii::$app->request->get('shopid');
        $token = \Yii::$app->request->get('token');
        $istrue = \Yii::$app->request->get('istrue');
        $change = \Yii::$app->request->get('change');
        $replace_id = \Yii::$app->request->get('replace_id');
        $operate = \Yii::$app->request->get('operate');
        return $this->render('screenover',[
            'shopid'=>$shopid,
            'token'=>$token,
            'dev' => $dev,
            'istrue'=>$istrue,
            'change'=>$change,
            'replace_id'=>$replace_id,
            'operate'=>$operate,
        ]);
    }

    /**
     * 屏幕管理  更换屏幕
     */
    public function actionScreenupdate(){
        $dev = \Yii::$app->request->get('dev');
        $shopid = \Yii::$app->request->get('shopid');
        $token = \Yii::$app->request->get('token');
        $isupdate = \Yii::$app->request->get('isupdate');
        $replace_id = \Yii::$app->request->get('replace_id');
        return $this->render('screenupdate',[
            'shopid'=>$shopid,
            'token'=>$token,
            'isupdate'=>$isupdate,
            'dev'=>$dev,
            'replace_id'=>$replace_id,
        ]);
    }
    /**
     * 屏幕管理  更换屏幕
     */
    public function actionScreenupdateimg(){
        $dev = \Yii::$app->request->get('dev');
        $shopid = \Yii::$app->request->get('shopid');
        $token = \Yii::$app->request->get('token');
        $isupdate = \Yii::$app->request->get('isupdate');
        $replace_id = \Yii::$app->request->get('replace_id');
        return $this->render('screenupdateimg',[
            'shopid'=>$shopid,
            'token'=>$token,
            'isupdate'=>$isupdate,
            'dev'=>$dev,
            'replace_id'=>$replace_id,
        ]);
    }

    /**
     * @return string
     * 屏幕新增驳回修改页面
     */
    public function actionScreenNewUpdate(){
        $dev = \Yii::$app->request->get('dev');
        $shopid = \Yii::$app->request->get('shopid');
        $token = \Yii::$app->request->get('token');
        $isupdate = \Yii::$app->request->get('isupdate');
        $replace_id = \Yii::$app->request->get('replace_id');
        $operate = \Yii::$app->request->get('operate');
        return $this->render('/new-screen/screen-new-update',[
            'shopid'=>$shopid,
            'token'=>$token,
            'isupdate'=>$isupdate,
            'dev'=>$dev,
            'replace_id'=>$replace_id,
            'operate'=>$operate,
        ]);
    }
    /**
     * 更换屏幕审核未通过修改页面
     */
    public function actionChangeViewNewUpdate(){
        $dev = \Yii::$app->request->get('dev');
        $shopid = \Yii::$app->request->get('shopid');
        $token = \Yii::$app->request->get('token');
        $isupdate = \Yii::$app->request->get('isupdate');
        $replace_id = \Yii::$app->request->get('replace_id');
        return $this->render('/change-screen/change-view-new-update',[
            'shopid'=>$shopid,
            'token'=>$token,
            'isupdate'=>$isupdate,
            'dev'=>$dev,
            'replace_id'=>$replace_id,
            'member_id' => \Yii::$app->user->id,
        ]);
    }

    /*
     * 更换屏幕激活失败更换屏幕
     */
    public function actionChangeScreen(){
        $dev = \Yii::$app->request->get('dev');
        $shopid = \Yii::$app->request->get('shopid');
        $token = \Yii::$app->request->get('token');
        $replace_id = \Yii::$app->request->get('replace_id');
        $operate = \Yii::$app->request->get('operate');
        return $this->render('change-screen',[
            'shopid'=>$shopid,
            'token'=>$token,
            'dev'=>$dev,
            'replace_id' => $replace_id,
            'operate' => $operate,
        ]);
    }
    /*
     * 屏幕更换未通过
     */
    public function actionChangeUpdate(){
        $dev = \Yii::$app->request->get('dev');
        $shopid = \Yii::$app->request->get('shopid');
        $token = \Yii::$app->request->get('token');
        $isupdate = \Yii::$app->request->get('isupdate');
        $replace_id = \Yii::$app->request->get('replace_id');
        return $this->render('change-update',[
            'shopid'=>$shopid,
            'token'=>$token,
            'isupdate'=>$isupdate,
            'dev'=>$dev,
            'replace_id'=>$replace_id,
        ]);
    }
    /**
     * 内部安装 总店铺详情
     */
    public function actionUnderlineheadoffice(){
        $dev = \Yii::$app->request->get('dev') ?? '';
        $headquarters_id = \Yii::$app->request->get('headquarters_id');
        $token = \Yii::$app->request->get('token');
        return $this->render('underlineheadoffice',[
            'headquarters_id'=>$headquarters_id,
            'token'=>$token,
            'dev' => $dev,
        ]);
    }

    /**
     * 广告维护确认
     * @return string
     */
    public function actionMaintainConfirm(){
        $params = \Yii::$app->request->get();
        return $this->render('/advert-maintain/confirm',[
            'id' => $params['id'],
            'token'=> $params['token'],
            'shop_id' => $params['shop_id'],
            'mongo_id' => $params['mongo_id'],
        ]);
    }

    public function actionMainDetail(){
        $params = \Yii::$app->request->get();
        return $this->render('/advert-maintain/main-detail',[
            'id' => $params['id'],
            'token'=> $params['token'],
            'shop_id' => 0,
            //'mongo_id' => $params['mongo_id'],
            'mongo_id' => '5cd91588b3dc8a5f011cc355',
        ]);
    }

}
