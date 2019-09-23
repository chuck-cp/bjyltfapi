<?php

namespace api\modules\v1\models;

//use pc\models\Order;
use Yii;
use api\modules\v1\models\Order as Order2;

/**
 * This is the model class for table "{{%order_complain}}".
 *
 * @property integer $id
 * @property integer $order_id
 * @property string $member_id
 * @property string $member_name
 * @property integer $salesman_complain_level
 * @property string $salesman_complain_content
 * @property string $service_complain_content
 * @property integer $service_complain_level
 * @property string $create_at
 */
class OrderComplain extends \api\core\ApiActiveRecord
{
//    public $member_type="1";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_complain}}';
    }
    /*
     * 投诉申请
     * */
    public function Ordercomplain2(){
        try{
            $this->member_id=Yii::$app->user->id;
            $this->member_name=Member::getMemberFieldById('name');
            $this->order_id=$this->id;
            $this->id="";
            if($this->save()){
                return true;
            }else{
                return false;
            }
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return 'ERROR';
        }
    }
    /*
     * 投诉广告对接人
     * */
    public function Fcomplaint(){
        try{
            $this->member_id=(string)Yii::$app->user->id;
            $keys=self::find()->where(['order_id' =>$this->id,'member_type'=>2])->select('id')->asArray()->one();
            if(!empty($keys))return 1;
            $this->order_id=$this->id;
            $ordermodel = new Order2();
            $data=$ordermodel::find()->where(['id'=>$this->order_id])->select('salesman_id,custom_service_name,custom_service_mobile')->asArray()->one();
            if($this->member_id != $data['salesman_id'])return 2;
            $this->complain_member_mobile=$data['custom_service_mobile'];
            $this->complain_member_name=$data['custom_service_name'];
            $this->id="";
            $this->member_type=2;
            $this->complain_type=1;
            if($this->save()){
                return false;
            }else{
                return 3;
            }
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return 'ERROR';
        }
    }
    /*
     * 订单-投诉广告对接人和业务合作外人$member_type(1,2)
    * */
    public function Fcomplaint2($complain_type=""){
        try{
            if($complain_type==1) {
                $keys=self::find()->where(['order_id' =>$this->id,'complain_type'=>$complain_type])->select('id')->asArray()->one();
            }else{
                $keys=self::find()->where(['order_id' =>$this->id,'complain_type'=>$complain_type])->select('id')->asArray()->one();
            }
            if(!empty($keys)){
                return 1;
            }
            $this->member_id=(string)Yii::$app->user->id;
            $this->order_id=$this->id;
            $ordermodel = new Order2();
            $data=$ordermodel::find()->where(['id'=>$this->order_id])->select('member_id,custom_service_name,custom_service_mobile,salesman_name,salesman_mobile')->asArray()->one();
            if($this->member_id != $data['member_id'])return 2;
            if($complain_type==1){
                $this->complain_member_mobile=$data['custom_service_mobile'];
                $this->complain_member_name=$data['custom_service_name'];
            }else{
                $this->complain_member_mobile=$data['salesman_mobile'];
                $this->complain_member_name=$data['salesman_name'];
            }
            $this->id="";
            $this->member_type=1;
            $this->complain_type=$complain_type;
            if($this->save()){
                return false;
            }else{
                return 3;
            }
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return 'ERROR';
        }
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id'], 'required'],
            [['order_id', 'complain_level'], 'integer'],
            [['create_at'], 'safe'],
            [['member_id'], 'string', 'max' => 10],
            [['complain_member_name'], 'string', 'max' => 50],
            [['complain_content'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'member_id' => 'Member ID',
            'member_name' => 'Member Name',
            'complain_level' => 'Complain Level',
            'complain_content' => 'Complain Content',
            'create_at' => 'Create At',
        ];
    }
    public function scenes(){
        return [
            'ordercomplain2'=>[
                'id'=>[
                    'required'=>'1',
                    'id' => 'int',
                ],
                'salesman_complain_content'=>[
                    'required'=>'1',
                    'result' => 'ORDER_START_AT_EMPTY',
                ],
                'service_complain_content'=>[
                    'required'=>'1',
                    'result' => 'ORDER_START_AT_EMPTY',
                ],
            ],
            'fcomplaint'=>[
                'id'=>[
                    'required'=>'1',
                    'order_id'=>'string',
                    'result' => 'ORDER_ID_EMPTY',
                ],
                'complain_level'=>[
                    'required'=>'1',
                    'complain_level'=>'string',
                    'result' => 'ORDER_COMPLAIN_LEVEL_EMPTY',
                ],
                'complain_content'=>[
                    'required'=>'1',
                    'complain_content'=>'string',
                    'result' => 'ORDER_COMPLAIN_CONTENT_EMPTY',
                ],
            ],
            'fcomplaint2'=>[
                'id'=>[
                    'required'=>'1',
                    'order_id'=>'string',
                    'result' => 'ORDER_ID_EMPTY',
                ],
                'complain_level'=>[
                    'required'=>'1',
                    'complain_level'=>'string',
                    'result' => 'ORDER_COMPLAIN_LEVEL_EMPTY',
                ],
                'complain_content'=>[
                    'required'=>'1',
                    'complain_content'=>'string',
                    'result' => 'ORDER_COMPLAIN_CONTENT_EMPTY',
                ],
            ],
            'fcomplaint3'=>[
                'id'=>[
                    'required'=>'1',
                    'order_id'=>'string',
                ],
                'complain_level'=>[
                    'required'=>'1',
                    'complain_level'=>'string',
                    'result' => 'ORDER_COMPLAIN_LEVEL_EMPTY',
                ],
                'complain_content'=>[
                    'required'=>'1',
                    'complain_content'=>'string',
                    'result' => 'ORDER_COMPLAIN_CONTENT_EMPTY',
                ],
            ],
        ];
    }
}
