<?php
namespace flmencrypt\STD3Des;

class STD3Des  
{  
    public $key = "ZmxtODkyMTk=";
    public $iv = "ZmxtODk5YTI=";

    /** 
    *加密 
    * @param <type> $value 
    * @return <type> 
    */  
    public function encrypt ($value)  
    {  
        $td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_CBC, '');  
        $iv = base64_decode($this->iv);  
        $value = $this->PaddingPKCS7($value);  
        $key = base64_decode($this->key);  
        mcrypt_generic_init($td, $key, $iv);  
        $ret = base64_encode(mcrypt_generic($td, $value));  
        mcrypt_generic_deinit($td);  
        mcrypt_module_close($td);  
        return $ret;  
    }  
   
    /** 
    *解密 
    * @param <type> $value 
    * @return <type> 
    */  
    public function decrypt ($value)  
    {  
        $td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_CBC, '');  
        $iv = base64_decode($this->iv);  
        $key = base64_decode($this->key);  
        mcrypt_generic_init($td, $key, $iv);  
        $ret = trim(mdecrypt_generic($td, base64_decode($value)));  
        $ret = $this->UnPaddingPKCS7($ret);  
        mcrypt_generic_deinit($td);  
        mcrypt_module_close($td);  
        return $ret;  
    }  
   
    private function PaddingPKCS7 ($data)  
    {
        $block_size = mcrypt_get_block_size('tripledes', 'cbc');
        $padding_char = $block_size - (strlen($data) % $block_size);  
        $data .= str_repeat(chr($padding_char), $padding_char);  
        return $data;  
    }  
   
    private function UnPaddingPKCS7($text)  
    {  
        $pad = ord($text{strlen($text) - 1});  
        if ($pad > strlen($text)) {  
            return false;  
        }  
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {  
            return false;  
        }  
        return substr($text, 0, - 1 * $pad);  
    }  
}  
?>