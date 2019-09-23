<?php

namespace api\modules\v1\models;

use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "{{%system_account}}".
 *
 * @property integer $total
 * @property integer $adv_expend
 * @property integer $margin
 */
class SystemAcount extends \api\core\ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%system_account}}';
    }

    public static function UpdateAccount($price){
        try{
            if(SystemAcount::find()->where(['id'=>1])->count()){
                SystemAcount::updateAllCounters(['total'=>$price],['id'=>1]);
            }else{
                $model = new SystemAcount();
                $model->id = 1;
                $model->total = $price;
                $model->save();
            }
            return true;
        }catch (Exception $e){
            \Yii::error($e->getMessage(),'payment');
            return false;
        }

    }
}
