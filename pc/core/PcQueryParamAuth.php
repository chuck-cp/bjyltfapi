<?php
namespace pc\core;
use common\libs\ToolsClass;
use yii\base\Action;
use yii\filters\auth\QueryParamAuth;

class PcQueryParamAuth extends QueryParamAuth
{
    /**
     * @var string the parameter name for passing the access token
     */
    public $tokenParam = 'token';


    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        if($request->isGet) {
            $accessToken = $request->get($this->tokenParam);
        }elseif($request->isPut) {
            $params = ToolsClass::getParamsByPut();
            $accessToken = isset($params['token']) ? $params['token'] : '';
        }elseif($request->isDelete){
            $params = ToolsClass::getParamsByDelete();
            $accessToken = isset($params['token']) ? $params['token'] : '';
        }else{
            $accessToken = $request->post($this->tokenParam);
        }
        if (is_string($accessToken)) {
            $identity = $user->loginByAccessToken($accessToken, get_class($this));
            if ($identity !== null) {
                return $identity;
            }
        }
        if ($accessToken !== null) {
            $this->handleFailure($response);
        }

        return null;
    }
}
