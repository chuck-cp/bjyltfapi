<?php
namespace wap\controllers;
use cms\modules\member\models\MemberTeam;
use wap\core\WapController;
use wap\models\MemberEquipment;
use yii\web\Controller;


/**
 * 安装团队控制器
 */
class TeamController extends WapController
{

    /**
     * 待指派列表
     */
    public function actionWaitinstall(){
        $team_id = \Yii::$app->request->get('team_id');
        $token = \Yii::$app->request->get('token');
//        $team_id=5;
//        $token='B6pVwSbsWn7Uima-tbOEdf4UtCUOAXdl';
        return $this->render('waitinstall',[
            'team_id'=>$team_id,
            'token'=>$token,
        ]);
    }
    /**
     * 已指派列表
     */
    public function actionAlreadyassign(){
         $team_id = \Yii::$app->request->get('team_id');
          $token = \Yii::$app->request->get('token');
//        $team_id=5;
//        $token='B6pVwSbsWn7Uima-tbOEdf4UtCUOAXdl';
        return $this->render('alreadyassign',[
            'team_id'=>$team_id,
            'token'=>$token,
        ]);
    }
    /**
     * 已安装列表
     */
    public function actionAlreadyinstall(){
        $team_id = \Yii::$app->request->get('team_id');
        $token = \Yii::$app->request->get('token');
//        $team_id=5;
//        $token='B6pVwSbsWn7Uima-tbOEdf4UtCUOAXdl';
        return $this->render('alreadyinstall',[
            'team_id'=>$team_id,
            'token'=>$token,
        ]);
    }

    /**
     *待安装详情
     */
    public function actionWaitinstallinfo(){
          $shopid = \Yii::$app->request->get('shopid');
          $token = \Yii::$app->request->get('token');
      //  $shopid=48;
//        $token='S9ol1yfq6JiM5K98qEyRWSzN1DPkkGSg';
      //  48?token=S9ol1yfq6JiM5K98qEyRWSzN1DPkkGSg
        return $this->render('waitinstallinfo',[
            'shopid'=>$shopid,
            'token'=>$token,
        ]);

    }
    /**
     * 成员列表
     */
    public function actionTeamlist(){
          $team_id = \Yii::$app->request->get('team_id');
          $token = \Yii::$app->request->get('token');
//        $team_id=5;
//        $token='B6pVwSbsWn7Uima-tbOEdf4UtCUOAXdl';
        return $this->render('teamlist',[
            'team_id'=>$team_id,
            'token'=>$token,
        ]);
    }
    /*
     * 修改解除小组
     */
    public function actionModifyRelieve()
    {
        $token = \Yii::$app->request->get('token');
        $tid = \Yii::$app->request->get('team_id');
        return $this->render('modify-relieve',[
            'token' => $token,
            'tid' => $tid,
        ]);
    }
}
