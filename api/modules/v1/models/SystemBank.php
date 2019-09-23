<?php

namespace api\modules\v1\models;

use Yii;

class SystemBank extends \api\core\ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%system_bank}}';
    }

    public static function systemBanks() {
        $bankModel = SystemBank::find()->asArray()->all();
        if (empty($bankModel)) {
            return [];
        }
        foreach ($bankModel as $bank) {
            $result[$bank['id']] = [
                'id' => (int)$bank['id'],
                'name' => $bank['bank_name'],
                'logo' => $bank['bank_logo'],
                'back' => ''
            ];
        }
        return $result;
    }
}
