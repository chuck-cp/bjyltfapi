<?php
namespace common\libs;
class DataClass{

    // 店铺审核状态
    public static function shopExamineStatus()
    {
        return [
            -1 => '待提交',
            0 => '待审核',
            1 => '审核未通过',
            2 => '待安装',
            3 => '安装待审核',
            4 => '安装未通过',
            5 => '安装完成',
            6 => '已关闭'
        ];
    }
    // 签到页面的店铺类型
    public static function signShopType() {
        return [
            1 => '租赁店',
            2 => '自营店',
            3 => '连锁店'
        ];
    }
    public static function activityDefaultNotice() {
        return [
            [
                'mobile' => '185***1024',
                'price' => '750',
            ],[
                'mobile' => '136***8818',
                'price' => '300',
            ],[
                'mobile' => '134***0024',
                'price' => '1200',
            ],[
                'mobile' => '177***0071',
                'price' => '450',
            ],[
                'mobile' => '133***4551',
                'price' => '1350',
            ],[
                'mobile' => '186***7386',
                'price' => '900',
            ],[
                'mobile' => '186***4818',
                'price' => '150',
            ],[
                'mobile' => '136***3382',
                'price' => '300',
            ],[
                'mobile' => '139***9098',
                'price' => '1500',
            ],[
                'mobile' => '189***9159',
                'price' => '750',
            ],[
                'mobile' => '131***9590',
                'price' => '600',
            ],[
                'mobile' => '135***8044',
                'price' => '150',
            ],[
                'mobile' => '176***1609',
                'price' => '450',
            ],[
                'mobile' => '186***8861',
                'price' => '900',
            ],[
                'mobile' => '173***9897',
                'price' => '1050',
            ],[
                'mobile' => '186***8261',
                'price' => '300',
            ],[
                'mobile' => '185***1930',
                'price' => '300',
            ],[
                'mobile' => '158***9787',
                'price' => '150',
            ],[
                'mobile' => '189***7592',
                'price' => '450',
            ],[
                'mobile' => '186***9684',
                'price' => '600',
            ]
        ];
    }

    public static function lowerLevel(){
        return [['key'=>0,'value'=>'全部'],['key'=>2,'value'=>'二级伙伴'],['key'=>3,'value'=>'三级伙伴'],['key'=>4,'value'=>'四级伙伴'],['key'=>5,'value'=>'五级伙伴'],['key'=>6,'value'=>'六级伙伴']];
    }
    public static function agreementTitle(){
        return [
            'install_agreement'=>'视频设备安装协议',
            'concurrent_post_agreement'=>'玉龙传媒业务合作政策',
            'member_agreement'=>'玉龙传媒用户服务协议',
            'install_condition'=>'LED屏安装条件',
            'throw_agreement'=>'玉龙传媒广告购买及投放规则',
            'privacy_policy'=>'隐私政策',
        ];
    }
    public static function defaultSpaceTime(){
        return [
            'a1'=>[10,10,10,10,10,10,10,10,10],
            'a2'=>[10,10,10,10,10,10,10],
            'b'=>[3600],
            'c'=>[3600],
            'd'=>[3600],
        ];
    }
    public static function defaultTimeList(){
        return [
            'a1'=>[60,120,150,180,240,300],
            'a2'=>[5,10,15,20,25,30,60],
            'b'=>[30],
            'c'=>[60],
            'd'=>[60],
        ];
    }
}