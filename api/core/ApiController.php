<?php

namespace api\core;
use common\libs\ToolsClass;
use Yii;
use yii\db\mssql\PDO;
use yii\filters\RateLimiter;
use yii\web\Response;
use yii\data\ActiveDataProvider;

class ApiController extends \yii\rest\Controller
{
    public $params;
    public function init(){
        parent::init();
        if(\Yii::$app->request->isGet){
            $this->params = \Yii::$app->request->get();
        }elseif(\Yii::$app->request->isPost){
            $this->params = \Yii::$app->request->post();
        }elseif(\Yii::$app->request->isPut){
            $this->params = ToolsClass::getParamsByPut();
        }elseif(\Yii::$app->request->isDelete){
            $this->params = ToolsClass::getParamsByDelete();
        }
    }

    //判断是否为内部人员
    public function isInsideAction(){
        if(Yii::$app->user->identity->inside == 0){
            echo json_encode($this->returnData('ERROR'));
            exit;
        }
    }

    public function getParams($key){
        return isset($this->params[$key]) ? $this->params[$key] : '';
    }
    public function resultCode($key){
        $codeArray =  [
            'SUCCESS'=>200,//成功
            'ERROR'=>400,//失败
            'MOBILE_EMPTY'=>401,//手机号不能为空
            'PASSWORD_EMPTY'=>402,//密码不能为空
            'REPEAT_PASSWORD_EMPTY'=>403,//重复密码不能为空
            'VERIFY_EMPTY'=>404,//验证码不能为空
            'MOBILE_ERROR'=>405,//手机号格式不正确
            'VERIFY_ERROR'=>406,//验证码不正确
            'NAME_PASSWORD_ERROR'=>407,//用户名或密码错误
            'FEEDBACK_CONTENT_EMPTY'=>408,//反馈内容不能为空
            'FUNCTION_CONTENT_EMPTY'=>409,//工作台内容不能为空
            'PARENT_NUMBER_ERROR'=>410,//邀请人工号不正确
            'MEMBER_PUT_BASIC_ERROR'=>411,//修改用户基础信息失败
            'ID_NUMBER_EMPTY'=>412,//身份证号码不能为空
            'ID_FRONT_IMAGE_EMPTY'=>413,//身份证正面照不能为空
            'ID_BACK_IMAGE_EMPTY'=>414,//身份证背面照不能为空
            'ID_HAND_IMAGE_EMPTY'=>415,//手持身份证照片不能为空
            'MEMBER_PUT_ID_ERROR'=>416,//修改身份证信息失败,信息已审核通过
            'SHOP_EXISTENT'=>417,//店铺不存在
            'MEMBER_LOWER_NO_PERMISSION'=>418,//没有权限查看该用户的信息
            'ADMIN_AREA_EMPTY'=>419,//地区不能为空
            'NOT_PUT_ADMIN_AREA_PERMISSION'=>420,//不是正式兼职人员
            'PUT_ADMIN_AREA_ERROR'=>421,//已选择过地区
            'PUT_ADMIN_AREA_NOT_EXIST'=>422,//地区不存在
            'PASSWORD_FORMAT_ERROR' => 423,//密码为6~16位字母数字符号
            'VERIFY_TIME_OUT' => 424,//验证码已过期
            'VERIFY_ERROR' => 425,//手机验证码不正确
            'MOBILE_IS_EXIST'=> 426,//该手机号已被使用
            'MESSAGE_TYPE_EMPTY'=> 427,//短信类型为空
            'TOW_PASSWORD_ERROR'=>428,//两次密码不一致
            'PASSWORD_ERROR' => 429,//密码错误
            'MOBILE_NO_CHANGE' => 430,//手机号没有修改
            'NOT_GET_MESSAGE' => 431,//没有获取到消息
            'BANK_ID_EMPTY'=>432,//请选择银行
            'BANK_NUMBER_EMPTY'=>433,//银行预留手机号不能为空
            'BANK_MOBILE_EMPTY'=>434,//提现金额不能为空
            'WITHDRAW_PRICE_EMPTY'=>435,//银行ID错误
            'BANK_ID_ERROR'=>436,//应用类型不能为空
            'APP_TYPE_EMPTY'=>437,
            'REGISTER_SUCCESS' => 438,//注册成功
            'REGISTER_ERROR' => 439,//注册失败
            'REGISTER_MOBILE_EXIST' => 440,//该手机号已被使用
            'PASSWORD_FORMAT_ERROR' => 441,//密码为6~16位字母数字符号
            'USER_NAME_FORMAT_ERROR' => 442,//用户名为4~16位大小字母、数字、下划线组合
            'NAME_PASSWORD_ERROR' => 443,//用户名不存在或密码不正确!
            'LOGIN_SUCCESS' => 444,//登录成功
            'PASSWORD_UPDATE_NO_MESSAGE' => 445,//未接收到消息!
            'NEW_PASSWORD_EMPTY' => 446, //新密码不为空
            'PASSWORD_UPDATE_SUCCESS' => 447,//登录密码修改成功
            'PASSWORD_UPDATE_ERROR' => 448,//登录密码修改失败
            'VERIFY_TYPE_EMPTY' => 449,//短信类型为空！
            'VERIFY_SEND_ERROR' => 450,//短信发送失败
            'VERIFY_SEND_SUCCESS' => 451,//短信发送成功!
            'VERIFY_MOBILE_IS_NOT_EXIST'=>452,//手机号不存在
            'MEMBER_SEX_EMPTY'=>453,//姓别不能为空
            'MEMBER_NAME_EMPTY'=>454,//姓名不能为空
            'BANK_MEMBER_NAME_EMPTY'=>455,//持卡人姓名不能为空
            'MEMBER_MOBILE_ERROR'=>456,//手机号错误
            'PAYMENT_PASSWORD_EMPTY'=>457,//支付密码不能为空
            'PAYMENT_PASSWORD_ERROR'=>458,//支付密码错误
            'WITHDRAW_PRICE_MIN_ONE'=>459,//提现金额不能小于1元
            'MEMBER_NO_BALANCE'=>460,//提现金额不能大于可用余额
            'NEW_PASSWORD_SAME'=>461,//新密码与旧密码相同
            'APPLY_CODE'=>462,//安装订单号不能为空
            'DYNAMIC_CODE'=>463,//安装订单状态码不能为空
            'ORDER_NOT_EXIST'=>464,//订单号不存在
            'ALREADY_INSTALLED'=>465,//该订单已安装完成
            'SCREEN_NUMBER_ERROR'=>483,//获取屏幕待安装数量失败
            'CREATE_SHOP_MEMBER_MOBILE_EMPTY'=>466,//业务员手机号不能为空
            'CREATE_SHOP_MEMBER_MOBILE_ERROR'=>467,//业务员手机号错误
            'SHOP_NAME_EMPTY'=>468,//店铺名称不能为空
            'AREA_EMPTY'=>469,//店铺所在地区不能为空
            'APPLY_NAME_EMPTY'=>470,//申请人姓名不能为空
            'IDENTITY_CARD_EMPTY'=>471,//申请人身份证号码不能为空
            'ADDRESS_EMPTY'=>472,//详细地址不能为空
            'ACREAGE_EMPTY'=>473,//店铺面积不能为空
            'APPLY_SCREEN_NUMBER_EMPTY'=>474,//屏幕数量不能为空
            'MIRROR_NUMBER_EMPTY'=>475,//镜面数量不能为空
            'SHOP_IMAGE_EMPTY'=>476,//店铺门脸照片不能为空
            'APPLY_MOBILE_EMPTY'=>477,//申请人手机号不能为空
            'COMPANY_NAME_EMPTY'=>478,//公司名称不能为空
            'REGISTRATION_MARK_EMPTY'=>479,//营业执照注册号不能为空
            'IDENTITY_CARD_FRONT_EMPTY'=>480,//身份证正面图不能为空
            'BUSINESS_LICENCE_EMPTY'=>481,//营业执照图不能为空
            'PANORAMA_IMAGE_EMPTY'=>482,//店铺全景图不能为空
            'REPEAT_SHOP'=>483,//请勿重复创建店铺
            'CODE_DOES_NOT_EXIST'=>484,//屏幕编码不存在
            'GET_SCREEN_FAIL'=>485,//获取屏幕失败
            'EQUIPMENT_NUMBER_EMPTY'=>486,//设备号不能为空
            'EQUIPMENT_TYPE_EMPTY'=>487,//设备类型不能为空
            'PUSH_ID_EMPTY'=>488,//推送ID不能为空
            'PUSH_STATUS_EMPTY'=>489,//推送状态不能为空
            'ADVERT_ID_EMPTY'=>490,//广告ID不能为空
            'ADVERT_TIME_EMPTY'=>491,//播放时长不能为空
            'ADVERT_RATE_EMPTY'=>492,//播放频率不能为空
            'ADVERT_TOTAL_DAY_EMPTY'=>493,//播放天数不能为空
            'ORDER_DATE_ERROR'=>494,//订单日期天数错误,
            'ORDER_SALESMAN_MOBILE_EMPTY'=>495,//业务员手机号不能为空,
            'ORDER_SALESMAN_MOBILE_ERROR'=>496,//业务员手机号错误,
            'ORDER_ADVERT_ID_EMPTY'=>497,//广告ID不能为空,
            'ORDER_RATE_EMPTY'=>498,//播放频率不能为空,
            'ORDER_ADVERT_TIME_EMPTY'=>499,//广告时长不能为空
            'ORDER_PAYMENT_EMPTY'=>500,//付款方式不能为空
            'ORDER_TOTAL_DAY_EMPTY'=>501,//播放天数不能为空
            'ORDER_PAYMENT_ERROR'=>502,//付款方式错误
            'ORDER_START_AT_EMPTY'=>503,//开始时间不能为空,
            'ORDER_END_AT_EMPTY'=>504,//结束时间不能为空
            'ORDER_AREA_EMPTY'=>505,//地区ID不能为空
            'SYSTEM_PREPAYMENT_RATIO_CONFIG_ERROR'=> 506,//系统没有配置定金支付的百分比
            'PAYMENT_LOG_ERROR'=>507,//付款日志写入失败,
            'ORDER_CANCELED'=>508,//该订单已取消,
            'ORDER_ALREADY_PAID'=>509,//该订单已支付
            'ORDER_CANCEL_CAUSE_EMPTY'=>601,//取消原因不能为空
            'STORAGE_ERROR'=>602,  //屏幕入库失败
            'ADVERT_AREA_SCREEN_ET_ZERO'=>603,//该地区还没有屏幕,
            'ADVERT_PRICE_CONFIG_ERROR'=>604,//后台未配置该广告的价格
            'ADVERT_RATE_ERROR'=>605,//费率选择错误,
            'ADVERT_PRICE_ERROR'=>606,//广告价格计算失败
            'ORDER_ID_EMPTY'=>607,//订单ID不能为空
            'ORDER_COMPLAIN_LEVEL_EMPTY'=>608,//评级不能为空
            'ORDER_COMPLAIN_CONTENT_EMPTY'=>609,//评论内容不能为空
            'FCOMPLAINT_ERROE_NOE'=>610,//用户不可重复提交，只可提交一次。
            'FCOMPLAINT_ERROE_TWO'=>611,//投诉人与订单信息不符。
            'COMPANY_AREA_ID_EMPTY'=>612,//广告购买人地区ID不能为空
            'ORDER_TIME'=>613,//修改后的投放时间不能大于订单广告时长！
            'ORDER_UPDATE_TIME_OVER'=>614,//修改投放时间失败您的修改次数已用完
            'CREATE_SHOP_MEMBER_MOBILE_ERROR_1'=>615,//业务合作人手机号不存在，请重新输入
            'WITHDRAW_PRICE_EXCEED_MAX'=>616,//提交金额过大,请上传身份证信息
            'SHOP_ID_EMPTY'=>617,//申请记录店铺id不能为空
            'PASSWORD_ERROR'=>618,//密码错误
            'REDUCE_TIME_ERROR'=>619,//库存不足
            'ORDER_CREATE_REPEAT'=>620,//有正在排期的订单
            'NO_BUY_SPACE_TIME'=>621,//没有可购买的广告时长
            'ORDER_OVERDUE'=>622,//该订单已逾期
            'ORDER_NOT_ALLOWED_MODIFY'=>623,//订单已超过最大修改次数
            'ORDER_UNPAID'=>624,//订单未付款
            'ORDER_NO_MODIFY'=>625,//订单未被修改
            'ORDER_TOTAL_DAYS_NOT_SAME'=>626,//订单总天数不一致
            'ORDER_DATE_NOT_ALLOWED'=>627,//订单日期不允许
            'ORDER_LOCKED'=>628,//订单已被锁定
            'ORDER_MODIFY_FAILED'=>629,//订单修改日期失败
            'ORDER_ALREADY_INVOICE' => 630,
            'ORDER_NO_PAYED' => 631, //订单未付款
            'ORDER_ADVERT_NO_RATE'=>632, //余量不足购买失败
            'UPDATE_NO_BUY_SPACE_TIME'=>633,
            'IDENTITY_CARD_BACK_EMPTY'=>634,//法人身份证背面不能为空
            'CODE_DOES_NOT_HOUSE'=>635,//仓库中没有此编码
            'VERIFY_REPEAT_SEND'=>636,//重复发送验证码
            'TOKEN_IN_BLACK_LIST'=>637,//用户已被列入黑名单
            'CERT_NUMBER_EMPTY' => 638, //电工证件编号不能为空
            'CERT_FRONT_IMAGE_EMPTY' => 639, //电工证件正面照不能为空
            'CERT_BACK_IMAGE_EMPTY' => 640, //电工证件背面照不能为空
            'PROFESSIONAL_NAME_EMPTY' => 641, //职业名称不能为空
            'MOBILE_OR_NAME_ERROR'=>642,//手机号或姓名不正确
            'MEMBER_CERT_NOT_EXAMINE'=>643,//该用户的电工证件还未通过审核
            'CERT_EXAMINE_SUCCESS'=>644,//审核已通过
            'LIVE_AREA_ID_EMPTY'=>645,//常住地区不能为空
            'LIVE_ADDRESS_EMPTY'=>646,//常住详细地址不能为空
            'TEAM_LEADER_DATE_ERROR'=>647,//组长信息有误,请确认组长信息后填写
            'MEMBER_NOT_CERTIFICATION'=>648,//您还没有实名认证,
            'EXIT_TEAM_WAIT_SHOP_LT_ZERO'=>649,//您还有指派任务未完成,无法退出小组
            'TEAM_JOIN_REPEAT'=>650,//请勿重复加入团队
            'TEM_MEMBER_ID_EMPTY'=>651,//组长的用户ID不能为空
            'TEAM_NAME_EMPTY'=>652,//团队名称不能为空
            'TEAM_AREA_ID_EMPTY'=>653,//团队现住址地区ID不能为空
            'TEAM_AREA_NAME_EMPTY'=>654,///团队现住地址的地区名称不能为空
            'TEAM_AREA_ADDRESS_EMPTY'=>655,//团队现住址详细地址不能为空
            'TEAM_MEMBER_NAME_EMPTY'=>656,//团队用户名称不能为空
            'TEAM_ALREADY_EXISTED'=>657,//团队已存在无法创建
            'TEAM_NON_EXISTENT'=>658,//团队不存在无法读取
            'TEAM_NAME_ALREADY_EXISTED'=>659,//团队名称已存在无法修改,
            'EXIT_TEAM_ERROR'=>660,//退出团队失败
            'TEAM_ID_EMPTY'=>662,//团队ID不能为空
            'TEAM_NOT_COMPLETE_TASK'=>663,//存在未完成任务无法解散团队
            'TEAM_JOIN_ERROR_WAIT_SCREEN_NUMBER_LT_ZERO'=>664,//有指派任务,无法加入团队
            'TEAM_CREATE_ERROR_ALREADY_JOIN_TEAM'=>665,//已经是成员,无法创建小组
            'SHOP_STATUS_ERROR'=>666,//店铺状态错误，不能更新
            'SHOP_STATUS_NOT_REJECT'=>668,//店铺状态不是被驳回
            'HEAD_QUARTERS_ID_EMPTY'=>669,//总店id不能为空
            'ASSIGN_NOT_EXIST'=>667,//指派人与安装人不符，无法安装
            'AUTHORIZE_IMAGE_EMPTY'=>670,//授权证明图片不能为空
            'CONTACTS_NAME_EMPTY'=>671,//店铺联系人姓名不能为空
            'CONTACTS_MOBILE_EMPTY'=>672,//店铺联系人手机号不能为空
            'DEL_DEVICE_ERROR' => 673, //综合事业部删除设备失败
            'SCREEN_NOT_EXIST' => 674, //系统设备库中找到
            'UPDATE_SCREEN_NUMBER_ERROR' => 675, //更换屏幕未通过修改时有误
            'ACCOUNT_TYPE_EMPTY' => 676, //绑定银行卡账户类型不能为空
            'ELECTRICIAN_CERTIFICATE_AREA_NAME_EMPTY' => '677', //电工认证地区不能为空
            'SCREEN_END_AT_EMPTY' => '678', //屏幕结束时间不能为空
            'SCREEN_END_AT_ERROR' => '679', //屏幕结束时间错误播放不能小于10小时
            'SCREEN_START_AT_ERROR' => 680, //开始时间错误
            'IMAGE_URL_EMPTY' => 681, //店铺图片不能为空
            'IMAGE_SHOP_ID_EMPTY' => 682, //店铺上传图片店铺id不能为空
            'SORT_JSON_EMPTY' => 683, //店铺排序字符串不能为空
            'BRANCH_NUMBERS_TOO_MANY' => '684', //分店數量已超上限
            'SHOP_TYPE_EMPTY' => 685, //店铺类型不能为空（上传图片）
            'HEAD_ID_ERROR' => 686, //总店id不能为空
            'branchName_EMPTY' => 687, //分店名称不能为空
            'branchArea_EMPTY' => 688, //分店地区不能为空
            'branchAddress_EMPTY' => 689, //分店详细地址不能为空
            'BRANCH_NUMBERS_TOO_MANY' => 690, //分店数量已达上限
            'IMAGE_SIZE_EMPTY' => 691 ,//图片大小不能为空
            'IMAGE_SHA_EMPTY' => 692, //图片加密串不能为空
            'REPLACE_SCREEN_NUMBER_ERROR' => 693, //更换屏幕数量有误
            //20181016
            'INVITE_MEMBER_NOT_EXIST' => 694, //邀请人不存在
            'INVITE_MEMBER_CAN_NOT_SELF' => 695, //邀请人不能是自己
            'INVITE_MEMBER_MUST_BE_INSIDE' => 696, //邀请人必须为内部人员
            'THIS_INVITE_IS_REPEAT' => 697, //该人已经是您的邀请人
            'REPEAT_SUBMIT' => 698, //请勿重复提交
            'FAILED_REASON_EMPTY' => 699, //失败原因不能为空
            'INVITER_AND_INIVTEE_CON_NOT_BE_EACH_OTHER' => 700,
            //20181109签到
            //team
            'TEAM_NAME_EXIST' => 701,
            'TEAM_NAME_EMPTY' => 702,
            'TEAM_ID_EMPTY' => 703,
            'SIGN_DATA_PERMISSION_ERROR' => 704,
            'TEAM_TYPE_EMPTY' => 705,
            'SIGN_TEAM_ID_EMPTY' => 706,
            //maintain
            'SIGN_MEMBER_ERROR' => 707,
            'SIGN_SHOP_TYPE_ERROR' => 708,
            'SIGN_EVALUATE_EMPTY' => 709,
            'SIGN_ID_EMPTY' => 710,
            'SIGN_SHOP_ID_EMPTY' => 711,
            'SIGN_SHOP_NAME_EMPTY' => 712,
            'SIGN_SHOP_ADDRESS_EMPTY' => 713,
            'LONGITUDE_EMPTY' => 714,
            'LATITUDE_EMPTY' => 715,
            'SIGN_CONTACTS_NAME_EMPTY' => 716,
            'SIGN_CONTACTS_MOBILE_EMPTY' => 717,
            'SIGN_MAINTAIM_CONTENT_EMPTY' => 718,
            'ADDRESS_SEARCH_EMPTY' => 719,
            'ADDRESS_SEARCH_ERROR' => 720,
            'SIGN_WORD_EMPTY' => 721,
            //business
            'SIGN_MEMBER_TYPE_EMPTY' => 722,
            'SIGN_CREATE_AT_EMPTY' => 723,
            'SIGN_MEMBER_ID_EMPTY' => 724,
            'SIGN_SHOP_ACREAGE_EMPTY' => 725,
            'SIGN_SHOP_MIRROR_EMPTY' => 726,
            'MINIMUM_EMPTY' => 727,
            'SIGN_SHOP_TYPE_EMPTY' => 728,
            'SIGN_INTERVAL_TIME_ERROR' => 729,
            'SIGN_MEMBER_ERROR' => 730,
            'MEMBER_QUIT' => 731, // 该用户已离职
            'SIGN_ERROR' => 732, // 签到失败
            'NOT_JOIN_SIGN_TEAM' => 733, // 您还没有加入任何小组
            'CAN_NOT_LOOK_OTHER' => 734,
            'IMAGE_MAX_THIRTY' => 735,
            'START_END_TIME_ERROR' => 736,
            //扫码奖励金
            'REWARD_MEMBER_ID_EMPTY' => 737,
            'REWARD_MEMBER_NOT_EXIST' => 738,
            'REWARD_NICK_NAME_EMPTY' => 739,
            'REWARD_NICK_NAME_TOO_LONG' => 740,
            'REWARD_SHOP_ID_ERROR' => 741,
            'REWARD_SHOP_ID_ERROR' => 742,
            'REWARD_ID_EMPTY' => 743,

            'SHOP_NOT_EXIST' => 744,
            'REWARD_AGREE_EMPTY' => 745,
            'REWARD_SHOP_ID_EMPTY' => 746,
            'REWARD_SHOP_TYPE_EMPTY' => 747,

            'SING_TEAM_ID_CHANGED' => 748,
            'ACTIVITY_NOT_EXIST' => 749, // 活动记录不存在或已签约

            'ADD_SCREEN_TO_LIST_FAIL' =>750,
            'SCREEN_CAN_NOT_EMPTY' => 751, //屏幕编码不能为空
            'DEL_DEVICE_NOT_FOUND' => 752, //要删除的设备未找到
            'REPLACE_SHOP_NOT_EXIST' => 753, //改店铺未找到
            'SHOP_AREA_NOT_FOUND' => 754, //未找到店铺地区 （价格）
            'INSTALL_MEMBER_NOT_FOUND' => 755, //未找到安装人
            'INSTALL_MEMBER_ID_CHANGED' => 756, //任务不存在
            'SHOP_NOT_NEED_OPERATE' => 757, //您没有更换屏幕
            'INCR_AND_DEL_NUMBER_NOT_SAME' => 758, //更换和删除设备数不一致
            'DEL_FROM_SCREEN_FAIL' => 759, //从店铺屏幕中删除失败
            'DEL_WRITE_LIST_FAIL' => 760, //删除数据写入队列失败
            'DEVICE_SYSTEM_ERROR' => 761,  //设备未出库或未在库中找到
            'SCREEN_SOFT_DEVICE_NUMBER_NOT_SAME' => 762, //软硬件编号不一致
            'SCREEN_ALREDY_IN_SHOP' => 763, //屏幕已在店铺中
        ];
        return isset($codeArray[$key]) ? $codeArray[$key] : 0;
    }

    public function returnData($statusCode='SUCCESS',$data=[]){
        $resultArray = [
                'status'=>$this->resultCode($statusCode),
                'message'=>Yii::t('yii',$statusCode),
                'data'=>empty($data) ? null : $data
        ];
        return $resultArray;
    }

    public function behaviors()
    {
        //使用验证权限过滤器
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => ApiQueryParamAuth::className(),
        ];
        $behaviors['contentNegotiator']['formats'] = ['application/json' => Response::FORMAT_JSON,];
        return $behaviors;
    }
    /**
     *pad端访问地图如果用户离职了强制退出
     */
    public function authentication(){
        if(Yii::$app->user->identity->quit_status){
            return false;
        }
        return true;
    }
}
