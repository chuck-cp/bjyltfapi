<?php

namespace pc\controllers;
use pc\core\PcController;
use pc\models\MemberCopyright;
use Yii;
use yii\data\Pagination;
use yii\widgets\LinkPager;
/**
 * 产权页面
 */
class PropertyController extends PcController
{
    public $enableCsrfValidation = false;
    public function actionIndex(){
        if(!$uid = \Yii::$app->user->id) {
                return $this->redirect(['/index/index']);
        }
        $request = Yii::$app->request;
        //登录的用户
        $userid = \Yii::$app->user->id;
        //$userid = 1;
        $copyrightModel = new MemberCopyright();
        $data = $copyrightModel->find()->where(['member_id'=>$userid])->orderBy("id ASC")->all();
        return $this->render('index',[
            'data'=>$data,
        ]);
        
    }
    /*
     * action delall
     * 删除多项
     */
    public function actionDelall(){
        if(!$uid = \Yii::$app->user->id) {
            return $this->redirect(['/index/index']);
        }
        $request = Yii::$app->request;
        $ids = $request->post("ids");
        $copyrightModel = new MemberCopyright();
        $data = $copyrightModel->delAll($ids);
        if($data){
            return json_encode($ids);
        }
    }
    /*
     * action delid
     * 删除单项
     */
    public function actionDelid(){
        if(!$uid = \Yii::$app->user->id) {
            return $this->redirect(['/index/index']);
        }
        $request = Yii::$app->request;
        $id = $request->post("id");
        $where = "id=".$id;
        $res = MemberCopyright::deleteAll($where);
        return $res;
    }
    /*
     * action Modifyname
     * 修改产权名称
     */
    public function actionModifyname(){
        if(!$uid = \Yii::$app->user->id) {
            return $this->redirect(['/index/index']);
        }
        $request = Yii::$app->request;
        //var_dump($request->post());
        $id = $request->post("id");
        $name = $request->post("name");
        //$id = !empty($request->post("id"))? $request->post("id"):0;
        //$name = !empty($request->post("name"))? $request->post("name"):null;
        $copyright = MemberCopyright::findOne($id);
        if(!empty($copyright)){
            $copyright->name = $name;
            $copyright->save();
        }
    }
        /*
         * action Copyright
         * 保存上传的产权
         */
    public function actionCopyright(){
        if(!$uid = \Yii::$app->user->id) {
            return $this->redirect(['/index/index']);
        }
        $request = \Yii::$app->request;
        $data['memberid']= \Yii::$app->user->id;
        //$data['memberid']= 1;//\Yii::$app->user->id;
        $imgurl= $request->get("imgurl");
        if(empty($imgurl)){
            return json_encode(array('status'=>400));
        }
        $imglocalname=$request->get("imglocalname");
        if(empty($imglocalname)){
            return json_encode(array('status'=>400));
        }
        $data['name'] = $imglocalname;
        $data['url'] = $imgurl;
        $copyright = new MemberCopyright();
        if($copyright->saveCopyright($data)){
            $data2 = MemberCopyright::find()->where(['member_id'=>$data['memberid']])->select('id')->orderBy("id desc")->limit(1)->asArray()->one();
            //print_r($data2);exit;
            return json_encode(array('status'=>200,'id'=>$data2['id']));
        }else{
            return json_encode(array('status'=>400));
        }
    }
    /*
     * action banquan
     * 版权声明页面
     */
    public function actionBanquan(){
        if(!$uid = \Yii::$app->user->id) {
            return $this->redirect(['/index/index']);
        }
        $rows = (new \yii\db\Query())
                ->select(['id', 'content'])
                ->from('yl_system_config')
                ->where(['id' => 'copyright_statement'])
                ->one();
        //var_dump($rows);
        //exit;
        return $this->render('banquan',[
            'data'=>$rows,
        ]);
    }
    /*
     * action banquan
     * 驳回申诉页面
     */
    public function actionComplaint(){
        if(!$uid = \Yii::$app->user->id) {
            return $this->redirect(['/index/index']);
        }
        $rows = (new \yii\db\Query())
                ->select(['id', 'content'])
                ->from('yl_system_config')
                ->where(['id' => 'copyright_complaint'])
                ->one();
        return $this->render('complaint',[
            'data'=>$rows,
        ]);
    }
    /*
     * action saveall
     * 批量保存
     */
    public function actionSaveall(){
        if(!$uid = \Yii::$app->user->id) {
            return $this->redirect(['/index/index']);
        }
        $request = Yii::$app->request;
        $namearr = $request->post("savedata");
        
        for($i=0;$i<count($namearr);$i++){
            //echo "aaaaaaa";exit;
            $copyright = MemberCopyright::findOne($namearr[$i][0]);
            print_r($copyright);
            if(!empty($copyright)){
                $copyright->name = $namearr[$i][1];
                $res = $copyright->save();
            }
        }
    }
}
