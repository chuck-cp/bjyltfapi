<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use common\libs\ToolsClass;
use Yii;
use yii\data\Pagination;
/**
 * This is the model class for table "{{%member_install_history}}".
 *
 * @property string $id
 * @property string $member_id
 * @property string $shop_id
 * @property string $shop_name
 * @property string $replace_id
 * @property string $area_name
 * @property string $address
 * @property integer $screen_number
 * @property integer $type
 * @property string $create_at
 */
class MemberInstallHistory extends ApiActiveRecord
{
    public $page;
    public $date;
    public $key_word;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_install_history}}';
    }

    /*
     * 获取用户安装历史
     */
    public function getMemberOrder(){
        $member_id = Yii::$app->user->id;
        $andWhere = [];
        $secWhere = [];
        if(!empty($this->date)){
            $andWhere = ["left(create_at,7)" => $this->date];
        }
        if(!empty($this->key_word)){
            $secWhere = ['or',['like','shop_name',$this->key_word],['like','area_name',$this->key_word],['like','address',$this->key_word]];
        }
        $history = self::find()->where(['member_id'=>$member_id])->andWhere($andWhere)->andWhere($secWhere)->orderBy('id DESC');
        $pagination = new Pagination(['totalCount'=>$history->count(),'pageSize' => 10]);
        $pagination->validatePage = false;
        $history = $history->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        if(!empty($history)){
            //处理日期
            foreach ($history as $k => $v){
                    $history[$k]['month'] = ToolsClass::judgeDate($v['create_at'],true,false);
            }
        }
        return $history;
    }

    // 获取业务历史
    public function getInstallHistory()
    {
        $data_type = (int)Yii::$app->request->get('type');
        $keyword = Yii::$app->request->get('keyword');
        $date = Yii::$app->request->get('date');
        if ($data_type != 1) {
            $data_type = 2;
        }
        $historyModel = self::find()->where(['member_id' => Yii::$app->user->id, 'data_type' => $data_type])->andFilterWhere(['like','shop_name',$keyword])->andFilterWhere(['left(create_at,7)' => $date]);
        $pagination = new Pagination([
            'totalCount' => $historyModel->count()
        ]);
        return $historyModel->limit($pagination->limit)->offset($pagination->offset)->select(['id','type','screen_number','left(`create_at`,7) as month','shop_id','shop_name','shop_image','area_name','address','create_at','shop_type','screen_type'])->orderBy('id desc')->asArray()->all();
    }

    /*
     * 场景
     */
    public function scenes(){
        return [
            'install' => [
                'date' => [],
                'key_word' => [],
            ],
        ];
    }
}
