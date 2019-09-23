<?php
$from_version = "v1";
$version = "v1";
return [
    //汇款详情
    'GET '.$from_version.'/member/<member_id>/remittance/<order_id>'=>$version.'/order/remittance',
    //订单排期
    'GET '.$from_version.'/member/<member_id>/prevorder/<order_id>'=>$version.'/order/prev-view',
    //订单详情
    'GET '.$from_version.'/member/<member_id>/order/<order_id>'=>$version.'/order/view',
    'GET '.$from_version.'/member/<member_id>/order/<order_id>/area'=>$version.'/order/select-area',
    'GET '.$from_version.'/member/<member_id>/confirm-area'=>$version.'/order/confirm-area',
    'GET '.$from_version.'/member/<member_id>/map'=>$version.'/order/map',
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
    //下单时同意购买协议
    'POST '.$from_version.'/order/agree'=>$version.'/order/agree-buy-contract',
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
    //实名认证时获取申请人名字
    'GET '.$from_version.'/shop/get-names'=>$version.'/shop/get-apply-names',
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
    'GET '.$from_version.'/screeninstall/change'=>$version.'/screeninstall/change',
    //屏幕安装 获取线下安装屏幕数量
    'GET '.$from_version.'/screeninstall/number/<shop_id>'=>$version.'/screeninstall/number',
    'GET '.$from_version.'/screen-new/number/<shop_id>'=>$version.'/screen-new/number',
    //换屏信息
    'GET '.$from_version.'/screeninstall/change-screen/<replace_id>'=>$version.'/screeninstall/change-screen',
    //屏幕安装 保存安装信息
    'POST '.$from_version.'/screeninstall/screeninster'=>$version.'/screeninstall/screeninster',
    //20181205 新增屏幕流程 ScreenIncr
    'POST '.$from_version.'/screeninstall/screen-incr'=>$version.'/screen-new/screen-incr',

    //修改安装屏幕
    'POST '.$from_version.'/screeninstall/change-post'=>$version.'/screen-change/change-post',
    //更换故障屏幕提交
    'POST '.$from_version.'/screen-change/change-post'=>$version.'/screen-change/change-post',
    //更换屏幕提交
    'POST '.$from_version.'/screen-change/change-post-new'=>$version.'/screen-change/change-post-new',
    //更换驳回修改提交 change-new-update-post
    'POST '.$from_version.'/screen-change/change-new-update-post'=>$version.'/screen-change/change-new-update-post',
    //更换时屏幕合法性验证
    'POST '.$from_version.'/screen-change/change-post-new-check'=>$version.'/screen-change/change-post-new-check',
    //屏幕安装 获取订单屏幕激活状态
    'GET '.$from_version.'/screeninstall/screenstatus/<shop_id>'=>$version.'/screeninstall/screenstatus',
    //屏幕安装 获取安装失败屏幕编码
    'GET '.$from_version.'/screeninstall/screenactivation'=>$version.'/screeninstall/screenactivation',
    //屏幕安装 安装获取所要修改的图片信息
    'GET '.$from_version.'/screeninstall/screenimgshow/<shop_id>'=>$version.'/screeninstall/screenimgshow',
    //屏幕安装 安装获取所要修改的图片信息
    'GET '.$from_version.'/screen-new/screenimgshow/<shop_id>'=>$version.'/screen-new/screenimgshow',
    //更换未通过界面
    'GET '.$from_version.'/screeninstall/change-update/<shop_id>'=>$version.'/screeninstall/change-update',
    //更换未通过提交图片
    'POST '.$from_version.'/screeninstall/change-not-pass-update'=>$version.'/screeninstall/change-not-pass-update',
    //屏幕安装 保存所要修改的图片信息
    'POST '.$from_version.'/screeninstall/screenimgupdate'=>$version.'/screeninstall/screenimgupdate',
    //内部安装 获取总店详情
    'GET '.$from_version.'/screeninstall/headoffice'=>$version.'/screeninstall/headoffice',
    //拆屏
    'GET '.$from_version.'/screeninstall/remove-screen-info'=>$version.'/screen-remove/remove-screen-info',
    //拆屏时验证拆除的屏幕合法性
    'POST '.$from_version.'/screeninstall/remove-screen-check'=>$version.'/screen-remove/remove-screen-check',


    'POST '.$from_version.'/payment/alipay' => $version.'/payment/alipay',
    'POST '.$from_version.'/payment/wechat' => $version.'/payment/wechat',
    'POST '.$from_version.'/payment/line' => $version.'/payment/line',
    'POST '.$from_version.'/callback/alipay' => $version.'/callback/alipay',
    'POST '.$from_version.'/callback/wechat' => $version.'/callback/wechat',
    'POST '.$from_version.'/callback/alipay-test' => $version.'/callback/alipay-test',
    'GET '.$from_version.'/advert' => $version.'/advert/index',
    //广告格式
    'GET '.$from_version.'/advert/get-pos' => $version.'/advert/advert',
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
    //总店信息创建
    'POST '.$from_version.'/shop-head/shop-head-create' => $version.'/shop-head/shop-head-create',
    //修改总店
    'POST '.$from_version.'/shop-head/shop-head-modify' => $version.'/shop-head/shop-head-modify',
    //获取所有的连锁店
    'GET '.$from_version.'/shop-head/shop-head-list' => $version.'/shop-head/shop-head-list',
    //获取所有分店信息
    'GET '.$from_version.'/shop-head/shop-branch-list' => $version.'/shop-head/shop-branch-list',
    //分店获取总店信息
    //'GET '.$from_version.'/shop-head/get-subbranch-info/<head_id>' => $version.'/shop-head/get-subbranch-info',
    'GET '.$from_version.'/shop-head/get-subbranch-info/<head_id>/branch/<branch_id>' => $version.'/shop-head/get-subbranch-info',
    //获取总店及其分店信息
    'GET '.$from_version.'/shop-head/get-head-branches/<head_id>/' => $version.'/shop-head/get-head-branches',
    //安装历史
    'GET '.$from_version.'/install-history/install' => $version.'/install-history/install',
    //点击安装列表查看店铺详情
    'GET '.$from_version.'/install-history/shop-detail/<id>' => $version.'/install-history/shop-detail',
    //业绩排行
    'GET '.$from_version.'/performance-rank/index/<time>' => $version.'/performance-rank/index',
    //我的店铺列表
    'GET '.$from_version.'/my-shop/list' => $version.'/my-shop/list',
    //我的总店铺详情
    'GET '.$from_version.'/my-shop/detail/<id>' => $version.'/my-shop/detail',
    //获取分店列表
    'GET '.$from_version.'/my-shop/get-branches' => $version.'/my-shop/get-branches',
    //单个保存店铺上传图片
    'POST '.$from_version.'/my-shop/save-shop-image' => $version.'/my-shop/save-shop-image',
    //店铺图片展示
    'GET '.$from_version.'/my-shop/show/<shop_id>' => $version.'/my-shop/shop-image-show',
    //店铺图片排序
    'PUT '.$from_version.'/my-shop/sort/<shop_id>' => $version.'/my-shop/image-sort',
    //店铺发布图片
    'POST '.$from_version.'/my-shop/release/<shop_id>' => $version.'/my-shop/release',
    //添加分店
    'POST '.$from_version.'/my-shop/add-branch' => $version.'/my-shop/add-branch',
    //店铺同意协议
    'POST '.$from_version.'/my-shop/agree' => $version.'/my-shop/agree',

    //创建业务或者维护团队
    'POST '.$from_version.'/sign/create' => $version.'/sign/create-team',
    //团队列表展示
    'GET '.$from_version.'/sign/team' => $version.'/sign/team',
    //团队详情展示
    'GET '.$from_version.'/sign/detail/<id>' => $version.'/sign/team-detail',
    //修改团队名称
    'POST '.$from_version.'/sign/update-team-name' => $version.'/sign/update-team-name',
    //设置或取消负责人
    'POST '.$from_version.'/sign/set-principal' => $version.'/sign/set-principal',
    //可添加为团队成员的内部人员展示
    'GET '.$from_version.'/sign/inside-members' => $version.'/sign/inside-members',
    //给团队添加成员
    'POST '.$from_version.'/sign/add-members' => $version.'/sign/add-members',
    //待删除人员展示
    'GET '.$from_version.'/sign/wait-delete-members/<id>' => $version.'/sign/wait-delete-members',
    //团队中删除成员
    'DELETE '.$from_version.'/sign/delete-members' => $version.'/sign/delete-members',
    //业务或维护签到界面
    'GET '.$from_version.'/sign/sign' => $version.'/sign/sign',
    //签到表单页面(业务)
    'GET '.$from_version.'/sign/business-sign/<team_id>' => $version.'/sign/business-sign',
    //签到表单页面(维护)
    'GET '.$from_version.'/sign/maintain-sign/<team_id>/<shop_id>' => $version.'/sign/maintain-sign',
    //签到表单提交(业务)
    'POST '.$from_version.'/sign/sign-post' => $version.'/sign/sign-post',
    //签到表单提交(维护)选择店铺列表
    'GET '.$from_version.'/sign/shop-list/<jd>/<wd>' => $version.'/sign/shop-list',
    //签到表单提交(维护)选择店铺搜索
    'GET '.$from_version.'/sign/shop-search' => $version.'/sign/shop-search',
    //签到表单提交(维护)
    'POST '.$from_version.'/sign/maintaim-post' => $version.'/sign/maintaim-post',
    //获取年月日
    'GET '.$from_version.'/system/get-date-time/<word>/<hour>' => $version.'/system/get-date-time',
    //团队足迹
    'GET '.$from_version.'/sign/team-footmark' => $version.'/sign/team-footmark',
    //团队足迹未签到人员列表
    'GET '.$from_version.'/sign/not-sign' => $version.'/sign/not-sign-members',
    //团队足迹未签到人员列表
    'GET '.$from_version.'/sign/choose-team' => $version.'/sign/choose-team',
    //团队足迹某团队详情(带分页)
    'GET '.$from_version.'/sign/team-sign-view' => $version.'/sign/team-sign-view',
    //团队足迹某团队详情(全部)
    'GET '.$from_version.'/sign/team-all-data' => $version.'/sign/team-all-data',
    //个人签到详情
    'GET '.$from_version.'/sign/single-sign-view' => $version.'/sign/single-sign-view',
    //个人单次签到详情
    'GET '.$from_version.'/sign/single-detail' => $version.'/sign/single-detail',
    //签到数据
    'GET '.$from_version.'/sign/sign-datas' => $version.'/sign/sign-datas',
    //不达标人列表
    'GET '.$from_version.'/sign/get-view' => $version.'/sign/get-view',
    //维护内容
    'GET '.$from_version.'/sign/maintain-content' => $version.'/sign/maintain-content',
    //重复店铺
    'GET '.$from_version.'/sign/repeat-shops' => $version.'/sign/repeat-shops',
    //店铺最新维护未评价信息
    'GET '.$from_version.'/sign/new-maintain' => $version.'/sign/new-maintain',
    //店铺最新维护未评价信息
    'GET '.$from_version.'/sign/new-maintain' => $version.'/sign/new-maintain',
    //店铺维护历史
    'GET '.$from_version.'/sign/maintain-history' => $version.'/sign/maintain-history',
    //店铺维详情
    'GET '.$from_version.'/sign/comment-detail' => $version.'/sign/comment-detail',
    //评价提交
    'POST '.$from_version.'/sign/evaluate' => $version.'/sign/evaluate',
    //查看数据开关
    'POST '.$from_version.'/sign/on-off' => $version.'/sign/on-off',
    'POST '.$from_version.'/sign' => $version.'/sign/create',
    //默认评价时间
    'POST '.$from_version.'/sign/close-evaluate' => $version.'/sign/close-evaluate',

    //合作人邀请
    'POST '.$from_version.'/my-shop/invite-code' => $version.'/my-shop/invite-code',
    //我和我的邀请人
    'GET '.$from_version.'/my-shop/my-and-inviter' => $version.'/my-shop/my-and-inviter',
    //查询邀请人
    'GET '.$from_version.'/my-shop/query/<mobile>' => $version.'/my-shop/query',
    'POST '.$from_version.'/activity/login' => $version.'/activity/login',
    'POST '.$from_version.'/activity/shop' => $version.'/activity/shop',

    'GET '.$from_version.'/shop/contract/list' => $version.'/shop/contract',
    'POST '.$from_version.'/shop/contract/<id>' => $version.'/shop/contract-failed',
    'GET '.$from_version.'/shop/active-detail/<id>' => $version.'/shop/active-detail',
    //奖励金协议同意
    'POST '.$from_version.'/shop/agree-reward' => $version.'/shop/agree-reward',

    //推荐奖金
    'POST '.$from_version.'/reward/update-name' => $version.'/reward/update-name',
    'GET '.$from_version.'/reward/reward-list' => $version.'/reward/reward-list',
    'GET '.$from_version.'/reward/order-list' => $version.'/reward/order-list',
    'GET '.$from_version.'/reward/all' => $version.'/reward/all-orders-list',
    'GET '.$from_version.'/reward/shop-search' => $version.'/reward/shop-search',

    //map
    'GET '.$from_version.'/map' => $version.'/map/get-shops-by-conditions',
    'GET '.$from_version.'/map/area' => $version.'/map/area',
    'GET '.$from_version.'/map/detail' => $version.'/map/map-detail',
    //微信活動移動端配置：圖片、title、description
    'GET '.$from_version.'/system/share' => $version.'/system/share',

];