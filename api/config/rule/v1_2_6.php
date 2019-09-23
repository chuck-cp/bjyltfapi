<?php
$from_version = "v1.2.6";
$version = "v1_2_6";
return [
    //汇款详情
    'GET '.$from_version.'/member/<member_id>/remittance/<order_id>'=>$version.'/order/remittance',
    //订单排期
    'GET '.$from_version.'/member/<member_id>/prevorder/<order_id>'=>$version.'/order/prev-view',
    //订单详情
    'GET '.$from_version.'/member/<member_id>/order/<order_id>'=>$version.'/order/view',
    'GET '.$from_version.'/member/<member_id>/order/<order_id>/area'=>$version.'/order/select-area',
    'GET '.$from_version.'/member/<member_id>/confirm-area'=>$version.'/order/confirm-area',
    'GET '.$from_version.'/member/<member_id>/confirm-area-view'=>$version.'/order/confirm-area-view',
    'POST '.$from_version.'/member/<member_id>/order/area'=>$version.'/order/create-area',
    //广告业务-投诉广告对接人
    'POST '.$from_version.'/member/<member_id>/order/fcomplaint'=>$version.'/order/fcomplaint',
    //订单-投诉广告对接人
    'POST '.$from_version.'/member/<member_id>/order/fcomplaint2'=>$version.'/order/fcomplaint2',
    //订单-投诉业务合作外人
    'POST '.$from_version.'/member/<member_id>/order/fcomplaint3'=>$version.'/order/fcomplaint3',
    //订单列表
    'GET '.$from_version.'/member/<member_id>/order'=>$version.'/order/index',
    //修改投放时间前的余量预览页面
    'GET '.$from_version.'/member/<member_id>/ordermodifyview/<order_id>'=>$version.'/order/order-modify-view',
    //修改投放时间
    'PUT '.$from_version.'/member/<member_id>/orderdate/<order_id>'=>$version.'/order/orderdate',
    //获取订单投诉人
    'GET '.$from_version.'/member/<member_id>/ordercomplain'=>$version.'/order/ordercomplain',
    //提交投诉信息
    'POST '.$from_version.'/member/<member_id>/ordercomplain'=>$version.'/order/ordercomplain2',
    //放弃订单
    'PUT '.$from_version.'/member/<member_id>/ordercannel'=>$version.'/order/ordercannel',
    //创建订单
    'POST '.$from_version.'/member/<member_id>/order'=>$version.'/order/create',
    //获取首页数据
    'GET '.$from_version.'/index'=>$version.'/index/index',
    //注册用户
    'POST '.$from_version.'/member'=>$version.'/member/register',
    //登陆
    'POST '.$from_version.'/member/login'=>$version.'/member/login',
    //退出
    'POST '.$from_version.'/member/logout'=>$version.'/member/logout',
    //找回密码
    'POST '.$from_version.'/member/password'=>$version.'/member/password',
    //获取微信端申请记录
    'GET '.$from_version.'/member/record'=>$version.'/shop/record',
    //获取微信端申请记录
    'GET '.$from_version.'/member/recordinfo'=>$version.'/shop/recordinfo',
    //设置支付密码
    'POST '.$from_version.'/member/<member_id>/payment_password'=>$version.'/member/payment-password',
    //验证支付密码
    'GET '.$from_version.'/member/<member_id>/payment_password'=>$version.'/member/get-payment-password',
    //获取我的姓名
    'GET '.$from_version.'/member/<mobile>/parent'=>$version.'/member/get-parent',
    //获取我的信息
    'GET '.$from_version.'/member/<member_id>'=>$version.'/member/view',
    //获取我的状态
    'GET '.$from_version.'/member/<member_id>/status'=>$version.'/member/get-status',
    //获取身份证信息
    'GET '.$from_version.'/member/<member_id>/id'=>$version.'/member/get-id',
    //修改我的信息
    'PUT '.$from_version.'/member/<member_id>'=>$version.'/member/update',
    //获取我的电工证信息
    'GET '.$from_version.'/member/<member_id>/cert'=>$version.'/member/get-cert',
    //修改我的电工证信息
    'PUT '.$from_version.'/member/<member_id>/cert'=>$version.'/member/update-cert',
    //修改我的手机号
    'PUT '.$from_version.'/member/<member_id>/mobile'=>$version.'/member/update-mobile',
    //修改我的身份证
    'PUT '.$from_version.'/member/<member_id>/id'=>$version.'/member/update-id',
    //修改极光ID
    'PUT '.$from_version.'/member/<member_id>/pushid'=>$version.'/member/update-pid',
    //修改极光推送状态
    'PUT '.$from_version.'/member/<member_id>/push'=>$version.'/member/update-push',
    //获取我的消息
    'GET '.$from_version.'/member/<member_id>/message'=>$version.'/member/message',
    //获取我的地区
    'GET '.$from_version.'/member/<member_id>/area' => $version.'/member/area',
    //修改我的地区
    'PUT '.$from_version.'/member/<member_id>/area' => $version.'/member/update-area',
    //获取我的伙伴
    'GET '.$from_version.'/member/<member_id>/lower'=>$version.'/lower/index',
    //获取我的伙伴的详情
    'GET '.$from_version.'/member/<member_id>/lower/<lower_member_id>'=>$version.'/lower/view',
    //提现
    'POST '.$from_version.'/member/<member_id>/withdraw'=>$version.'/withdraw/create',
    //获取提现信息
    'GET '.$from_version.'/member/<member_id>/withdraw'=>$version.'/withdraw/view',
    //获取我的业绩详情
    'GET '.$from_version.'/member/<member_id>/account'=>$version.'/account/view',
    //获取我的业绩列表
    'GET '.$from_version.'/member/<member_id>/account/list' => $version.'/account/index',
    //安装业务、屏幕管理接口
    'GET '.$from_version.'/member/<member_id>/shop'=>$version.'/shop/index',
    //我的店铺接口
    'GET '.$from_version.'/member/<member_id>/myshop'=>$version.'/shop/my-shop',
    //获取店铺地区列表
    'GET '.$from_version.'/member/<member_id>/shop/area' => $version.'/shop/area',
    //获取我的业务
    'GET '.$from_version.'/member/<member_id>/business'=>$version.'/shop/lower',
    //获取店铺详情
    'GET '.$from_version.'/shop/<shop_id>' => $version.'/shop/view',
    //修改店铺
    'POST '.$from_version.'/shop/<shop_id>/modify' => $version.'/shop/modify',
    //创建店铺
    'POST '.$from_version.'/shop' => $version.'/shop/create',
    //内部人创建店铺(只对内部人开放)
    'POST '.$from_version.'/shop/inner-create' => $version.'/shop/inner-create',
    //获取我的银行卡信息列表
    'GET '.$from_version.'/member/<member_id>/bank' => $version.'/bank/index',
    //绑定银行卡
    'POST '.$from_version.'/member/<member_id>/bank' => $version.'/bank/create',
    //解除银行卡
    'DELETE '.$from_version.'/member/<member_id>/bank/<bank_id>' => $version.'/bank/delete',
    //获取所有银行
    'GET '.$from_version.'/bank' => $version.'/bank/system',
    //获取店家佣金奖励
    'GET '.$from_version.'/system/brokerage' => $version.'/system/brokerage',
    //系统配置
    'GET '.$from_version.'/system/telephone' => $version.'/system/telephone',
    //获取腾讯云token
    'GET '.$from_version.'/system/sign-video'=>$version.'/system/sign-video',
    //广告投放统计
    'POST '.$from_version.'/system/throw-count'=>$version.'/system/throw-count',
    //获取启动页
    'GET '.$from_version.'/system/startup' => $version.'/system/startup',
    //获取系统公钥
    'GET '.$from_version.'/system/public-key'=>$version.'/system/public-key',
    //广告模块是否开启
    'GET '.$from_version.'/system/open-advert'=>$version.'/system/open-advert',
    //获取验证码
    'GET '.$from_version.'/verify'=>$version.'/system/verify',
    //获取系统版本
    'GET '.$from_version.'/version' => $version.'/system/version',
    //获取地区
    'GET '.$from_version.'/area'=>$version.'/system/area',
    //获取腾讯云token
    'GET '.$from_version.'/qcloud'=>$version.'/system/token',
    'GET '.$from_version.'/qcloud-material'=>$version.'/system/token-material',
    //意见反馈
    'POST '.$from_version.'/feedback'=>$version.'/system/feedback',
    //获取系统模块
    'GET '.$from_version.'/function'=>$version.'/function/index',
    //选择模块
    'POST '.$from_version.'/function'=>$version.'/function/update',
    //验证安装订单
    'GET '.$from_version.'/screeninstall/existence'=>$version.'/screeninstall/existence',
    //获取安装订单详情
    'GET '.$from_version.'/screeninstall'=>$version.'/screeninstall/get',

    //保存安装信息
    'POST '.$from_version.'/screeninstall'=>$version.'/screeninstall/post',
    //验证屏幕是否激活
    'GET '.$from_version.'/screeninstall/activation'=>$version.'/screeninstall/activation',
    //获取安装屏幕数量
    'GET '.$from_version.'/screeninstall/screennumber/<shop_id>'=>$version.'/screeninstall/screennumber',
    //获取线下安装屏幕数量
    'GET '.$from_version.'/screeninstall/screennumberunline/<shop_id>'=>$version.'/screeninstall/screennumberunline',
    //保存线下安装信息
    'POST '.$from_version.'/screeninstallunline'=>$version.'/screeninstall/unline',
    //验证线下安装屏幕是否激活
    'GET '.$from_version.'/screeninstall/activationunline'=>$version.'/screeninstall/activationunline',
    //线下获取安装订单详情
    'GET '.$from_version.'/screeninstall/underlinecheck'=>$version.'/screeninstall/underlinecheck',
    //线下获取安装订单状态
    'GET '.$from_version.'/screeninstall/underlinestatus/<shop_id>'=>$version.'/screeninstall/underlinestatus',
    //线下获取安装失败屏幕编码
    'GET '.$from_version.'/screeninstall/activationunlinenumber/<shop_id>'=>$version.'/screeninstall/activationunlinenumber',
    //线下安装获取所要修改的图片信息
    'GET '.$from_version.'/screeninstall/underlineimgshow/<shop_id>'=>$version.'/screeninstall/underlineimgshow',
    //线下安装保存所要修改的图片信息
    'POST '.$from_version.'/screeninstall/underlineimgupdate'=>$version.'/screeninstall/underlineimgupdate',

    //屏幕安装 安装屏幕列表
    'GET '.$from_version.'/screeninstall/screenshoplist'=>$version.'/screeninstall/screenshoplist',
    //屏幕安装 获取安装订单详情
    'GET '.$from_version.'/screeninstall/screencheck'=>$version.'/screeninstall/screencheck',
    //屏幕安装 获取线下安装屏幕数量
    'GET '.$from_version.'/screeninstall/number/<shop_id>'=>$version.'/screeninstall/number',
    //屏幕安装 保存安装信息
    'POST '.$from_version.'/screeninstall/screeninster'=>$version.'/screeninstall/screeninster',
    //屏幕安装 获取订单屏幕激活状态
    'GET '.$from_version.'/screeninstall/screenstatus/<shop_id>'=>$version.'/screeninstall/screenstatus',
    //屏幕安装 获取安装失败屏幕编码
    'GET '.$from_version.'/screeninstall/screenactivation'=>$version.'/screeninstall/screenactivation',
    //屏幕安装 安装获取所要修改的图片信息
    'GET '.$from_version.'/screeninstall/screenimgshow/<shop_id>'=>$version.'/screeninstall/screenimgshow',
    //屏幕安装 保存所要修改的图片信息
    'POST '.$from_version.'/screeninstall/screenimgupdate'=>$version.'/screeninstall/screenimgupdate',

    'POST '.$from_version.'/payment/alipay' => $version.'/payment/alipay',
    'POST '.$from_version.'/payment/wechat' => $version.'/payment/wechat',
    'POST '.$from_version.'/payment/line' => $version.'/payment/line',
    'POST '.$from_version.'/callback/alipay' => $version.'/callback/alipay',
    'POST '.$from_version.'/callback/wechat' => $version.'/callback/wechat',
    'POST '.$from_version.'/callback/alipay-test' => $version.'/callback/alipay-test',
    'GET '.$from_version.'/advert' => $version.'/advert/index',
    'POST '.$from_version.'/advert' => $version.'/advert/select',
    //验证手机验证码
    'GET '.$from_version.'/verifyCode/<verifyCode>/mobile/<mobile>' => $version.'/system/verify-code',
    //测试MQ
    'GET '.$from_version.'/screeninstall/mq'=>$version.'/screeninstall/activmq',
    //获取待开发票订单
    'GET '.$from_version.'/invoice/choose-order' => $version.'/invoice/choose-order',
    //获取开发票历史
    'GET '.$from_version.'/invoice/invoice-history' => $version.'/invoice/invoice-history',
    //生成发票
    'POST '.$from_version.'/invoice/create' => $version.'/invoice/create',
    //发票详情
    'GET '.$from_version.'/invoice/detail/<id>' => $version.'/invoice/detail',
    //待开合同列表
    'GET '.$from_version.'/contact' => $version.'/contact/index',
    //申请合同
    'POST '.$from_version.'/contact/contact' => $version.'/contact/contact',
    //合同历史列表
    'GET '.$from_version.'/contact/contact-history' => $version.'/contact/contact-history',
    //合同和发票规则处联系电话获取
    'GET '.$from_version.'/system/telephone' => $version.'/system/telephone',
    //获取培训资料列表
    'GET '.$from_version.'/business-trainning' => $version.'/business-trainning/index',


    //创建团队
    'POST '.$from_version.'/team'=>$version.'/team/create',
    //团队信息
    'GET '.$from_version.'/team/<team_id>'=>$version.'/team/view',
    //修改团队信息
    'PUT '.$from_version.'/team/<team_id>'=>$version.'/team/update',
    //加入团队
    'POST '.$from_version.'/team/join'=>$version.'/team/join',

    //退出团队
    'DELETE '.$from_version.'/team/<team_id>'=>$version.'/team/exit',
    //店铺列表
    'GET '.$from_version.'/team/<team_id>/shop'=>$version.'/team/shop',
    //指派店铺
    'POST '.$from_version.'/team/<team_id>/shop/<shop_id>'=>$version.'/team/assign',
    //取消指派店铺
    'PUT '.$from_version.'/team/<team_id>/shop/<shop_id>'=>$version.'/team/cancel',
    'GET '.$from_version.'/team/<team_id>/member'=>$version.'/team/list',
    //验证团队名称是否存
    'GET '.$from_version.'/team/isteamname/<team_name>'=>$version.'/team/isteamname',
    //获取团队成员
    'GET '.$from_version.'/team/teamlsit/<team_id>'=>$version.'/team/teamlsit',
    //解散团队
    'GET '.$from_version.'/team/dismiss/<team_id>'=>$version.'/team/dismiss',
];