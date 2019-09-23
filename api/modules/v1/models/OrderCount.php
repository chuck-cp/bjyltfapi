<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use Yii;

/**
 * 订单播放量统计
 */
class OrderCount extends ApiActiveRecord
{
    public static function tableName()
    {
        return '{{%order_count}}';
    }

    /*
     * 场景
     * */
    public function scenes()
    {
        return [
            'create' => [
                'token' => [
                    'required' => '1',
                    'result' => 'TOKEN_EMPTY'
                ],
                'order_code' => [
                    'required' => '1',
                    'result' => 'ORDER_CODE_EMPTY'
                ],
                'play_number' => [
                    'required' => '1',
                    'result' => 'PLAY_NUMBER_EMPTY'
                ],
                'count_at' => [
                    'required' => '1',
                    'result' => 'COUNT_AT_EMPTY'
                ],
            ]
        ];
    }
}
