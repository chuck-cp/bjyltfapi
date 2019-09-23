<?php
namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\modules\v1\models\Member;
use api\modules\v1\models\MemberLower;
use api\modules\v1\models\Shop;
use common\libs\DataClass;
use Yii;

/**
 * 我的伙伴
 */
class LowerController extends ApiController
{
    /**
     * 获取我的伙伴详情
     */
    public function actionView($member_id,$lower_member_id){
        $memberModel = new Member();
        $resultMember = $memberModel->getLowerMemberById($lower_member_id);
        if($resultMember['parent_id'] != Yii::$app->user->id && $resultMember['id'] != Yii::$app->user->identity->parent_id){
            return $this->returnData('MEMBER_LOWER_NO_PERMISSION');
        }
        $shopModel = new Shop();
        $resultLower = [
          'member'=>$resultMember,
          'shop'=>$shopModel->getMemberLowerShop($lower_member_id)
        ];
        return $this->returnData('SUCCESS',$resultLower);
    }

    /**
     * 获取我的伙伴列表
     */
    public function actionIndex(){
        $memberModel = new Member();
        $memberModel->loadParams($this->params,'lower-index');
        if((int)Yii::$app->request->get('page') < 2){
            $result = [
                'parent'=>$memberModel->getLowerMemberById(Yii::$app->user->identity->parent_id),
                'lower'=>$memberModel->getMemberLower(),
            ];
        }else{
            $result = [
                'parent'=>null,
                'lower'=>$memberModel->getMemberLower(),
            ];
        }
        return $this->returnData('SUCCESS',$result);
    }
}
