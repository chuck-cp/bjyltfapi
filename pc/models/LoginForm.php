<?php
namespace pc\models;

use common\libs\ToolsClass;
use Yii;
use yii\base\Model;
use yii\bootstrap\Alert;
/**
 * Login form
 */
class LoginForm extends Model
{
    public $user_name;
    public $password;
    public $rememberMe = true;
    public $verifyCode;
    private $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['user_name', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            //['verifyCode', 'captcha','message'=>'验证码错误'],
        ];
    }

    public function attributeLabels(){
        return [
            'password'=>Yii::t('yii','password'),
            'user_name'=>Yii::t('yii','UserName'),
            // 'verifyCode' => '验证码',
        ];
    }
    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->user_name,$this->password)) {
                 $this->addError($attribute, Yii::t('yii','Incorrect username or password.'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        \Yii::$app->session->setFlash("login_error",'用户名或密码错误');
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = Member::findIdentityByMobile($this->user_name);
        }

        return $this->_user;
    }

    public function maplogin()
    {
        $mobilearray = ['13581948044','13691303382','13901359098'];
        if(in_array($this->user_name,$mobilearray)) {
            if (!ToolsClass::checkVerify($this->user_name, $this->password)) {
                \Yii::$app->session->setFlash("login_error", '验证码错误');
                return false;
            }
            $userModel = $this->getUser();
            if (empty($userModel)) {
                \Yii::$app->session->setFlash("login_error", '手机号不存在');
                return false;
            }
            return Yii::$app->user->login($userModel, $this->rememberMe ? 3600 * 24 * 30 : 0);
        }else{
            return false;
        }
    }
}
