<?php
namespace api\modules\v1\controllers;
use api\modules\v1\core\ApiController;
use api\modules\v1\models\Feedback;
use common\libs\ToolsClass;

/**
 * 意见反馈
 */
class FeedbackController extends ApiController
{
    /**
     * 提交反馈
     */
    public function actionPost(){
        $feedbackModel = new Feedback();
        if($result = $feedbackModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        if($feedbackModel->save()){
            return $this->returnData();
        }
        return $this->returnData('ERROR');
    }
}
