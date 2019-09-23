<?php
namespace api\core;

use common\libs\PublicClass;
use common\libs\ToolsClass;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\IntegrityException;
use yii\mongodb\Query;

class ApiActiveRecord extends ActiveRecord
{
    /*
     * 设置字段
     * */
    protected function setFieldValue($params,$fieldName,$field){
        if(isset($params[$fieldName]) && (!empty($params[$fieldName]) || is_numeric($params[$fieldName]))){
            if($params[$fieldName] == NULL){
                $params[$fieldName] = '';
            }
            $value = $params[$fieldName];
            if(isset($field['type'])){
                if($field['type'] == 'int'){
                    $value = (int)trim($value);
                }elseif($field['type'] == 'string'){
                    $value = addslashes(trim($value));
                }
            }

            $this->$fieldName = $value;
        }elseif(isset($field['default'])){
            $this->$fieldName = $field['default'];
        }
    }
    /*
     * 获取参数
     * */
    protected function getNameAndParams($functionName,$fieldName){
        if(strstr($functionName,'(')){
            $functionName = str_replace(')','',$functionName);
            $nameAndParams = explode('(',$functionName);
            $functionName = $nameAndParams[0];
            $params = strstr($nameAndParams[1],",") ? explode(',',$nameAndParams[1]) : [$nameAndParams[1]];
            foreach($params as $param){
                $resultParams[] = $this->$param;
            }
        }else{
            $resultParams = $this->$fieldName;
        }
        return [$functionName,$resultParams];
    }
    /*
     * 检查字段
     * */
    protected function checkFieldValue($fieldName,$field){

        if(isset($field['required'])){
            if(is_array($field['required'])){
                foreach($field['required'] as $field){
                    if(!empty($this->$field)){
                        return [true, ''];
                    }
                }
                return [false, strtoupper($fieldName).'_EMPTY'];
            }elseif($field['required'] == 1 && empty($this->$fieldName)){
                return [false, strtoupper($fieldName).'_EMPTY'];
            }
        }
        if(isset($field['function'])){
            $functionClass = $field['function'];
            if(strstr($functionClass,'::')){
                $functionClass = explode("::",$functionClass);
                $functionName = $functionClass[1];
                list($functionName,$params) = $this->getNameAndParams($functionName,$fieldName);
                if($functionClass[0] == 'this'){
                    return [$this->$functionName($params), ''];
                }else{
                    return [$functionClass[0]::$functionName($params), ''];
                }
            }else{
                list($functionName,$params) = $this->getNameAndParams($functionClass,$fieldName);
                return [PublicClass::$functionName($params), ''];
            }
        }

        if (isset($field['max_length'])){
            return [strlen($this->$fieldName) <= $field['max_length'], strtoupper($fieldName).'_TOO_LONG'];
        }
        return [true,''];
    }
    /*
     * 验证数据,并将获取到的数据加载到model
     * @params array 数据集合
     * @sceneName string 场景名称
     * */
    public function loadParams($params,$sceneName){
        $scenes = $this->scenes();
        if(empty($scenes) || (!is_array($scenes)) || (!isset($scenes[$sceneName]))){
            return false;
        }

        $fields = $scenes[$sceneName];
        //加载数据
        foreach($fields as $key=>$field){
            if(isset($field[0]) && is_array($field[0])){
                $f = isset($field[1]) ? $field[1] : '';
                $this->setFieldValue($params,$key,$f);
            }else{
                $this->setFieldValue($params,$key,$field);
            }
        }
        //验证数据
        foreach($fields as $key=>$field){
            if(isset($field[0]) && is_array($field[0])){
                foreach($field[0] as $verify){
                    list($result,$message) = $this->checkFieldValue($key,$verify);
                    if(!$result){
                        return empty($message) ? $verify['result'] : $message;
                    }
                }
            }else{
                list($result,$message) = $this->checkFieldValue($key,$field);
                if(!$result){
                    return empty($message) ? $field['result'] : $message;
                }
            }
        }
    }


    /**
     * @param $table 表名 string
     * @param $select_field 要查询的字段多个逗号隔开 string
     * @param array $field_values 查询条件必须为关联数组，索引是table里的某字段
     * @return array|bool|false
     */
    public function getTableField($table, $select_field, $field_values = []){
        $connection = \Yii::$app->db;
        if(!is_array($field_values)){
            return false;
        }
        $where = '';
        end($field_values);
        $last_key = key($field_values);
        $params = [];
        foreach ($field_values as $k => $v){
            if(count($field_values) == 1){
                $where = $k.'=:'.$k;
            }else{
                if($last_key !== $k){
                    $where .= $k.'=:'.$k .' and ';
                }else{
                    $where .= $k.'=:'.$k;
                }
            }
            $params[':'.$k] = $v;
        }
        $sql = 'select '.$select_field.' from '.$table.' WHERE '.$where;
        return $connection->createCommand($sql)->bindValues($params)->queryOne();
    }
    /**
     * 判断二维数组是否为空
     * @param $arr
     * @return bool
     */
    public function judgeArrIsEmpty($arr){
        if(empty($arr)){
            return true;
        }
        foreach ($arr as $v){
            if(!$v){
                return true;
            }
        }
        return false;
    }

}
