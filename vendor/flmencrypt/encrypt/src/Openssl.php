<?php
namespace flmencrypt\encrypt;

use yii\base\Component;

class Openssl extends Component {
    public $iv;
    public $secret;

    public function init(){
        $this->secret = hash('md5',$this->iv,true);
    }

    public function decode($secretData){
        return openssl_decrypt($secretData,'aes-128-cbc',$this->secret,false,$this->iv);
    }

    public function encode($data){
        return openssl_encrypt($data,'aes-128-cbc',$this->secret,false,$this->iv);
    }
}