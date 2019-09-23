<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%log_system}}".
 *
 * @property integer $id
 * @property string $token
 * @property string $content
 * @property string $create_at
 */
class LogSystem extends ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%log_system}}';
    }

    public function saveLog(){
        return $this->save();
    }

    /*
    * 场景
    * */
    public function scenes()
    {
        return [
            'post' => [
                'content' => [
                    'required'=>'1',
                    'result'=>'SYSTEM_LOG_CONTENT_EMPTY'
                ],
                'equipment_number'=>[
                    'required'=>'1',
                    'result'=>'SYSTEM_EQUIPMENT_NUMBER_EMPTY'
                ],
                'token'=>[
                    'type' => 'string',
                ],
            ],
        ];
    }
}
