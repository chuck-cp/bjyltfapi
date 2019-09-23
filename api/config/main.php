<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

$rules = array_merge(
    require(__DIR__ . '/rule/v1.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
            'class' => 'api\modules\v1\v1',
        ],
    ],
    'components' => [
        'cos_gg'=>[
            'class'=>'xplqcloud\cos\Cos',
            'app_id' => '1252719796',
            'secret_id' => 'AKID2zkNq9TNDqMk3uFQRIzVwFLFzXs1ZXYN',
            'secret_key' => 'mPtBGLF1b0FI9QZXJvMbeV0Tluq041nU',
            'region' => 'bj',
            'bucket'=>'yulong',
            'insertOnly'=>true,
            'timeout' => 200
        ],
        'cos'=>[
            'class'=>'xplqcloud\cos\Cos',
            'app_id' => '1255626690',
            'secret_id' => 'AKIDqfO1Y9xGh2GYu6ewa3LArNm04xfBNhgU',
            'secret_key' => 'rY4PZYQ8fVAey78zkKjwzvoV1Misr00p',
            'region' => 'sh',
            'bucket'=>'yulongchuanmei',
            'insertOnly'=>true,
            'timeout' => 200
        ],
        'alipay'=>[
            'class'=>'xplalipay\alipay\Alipay',
            'back_url'=> 'https://api.bjyltf.com/v1/callback/alipay',
            'gateway_url' => 'https://openapi.alipay.com/gateway.do',
            'app_id' => '2018030402312844',
            'rsa_private_key' => 'MIIEogIBAAKCAQEArKWLwsgL5SwMFLL6d2+0xF6q4bW2ASLB7fQkL3V2qiQVda8e8ANSEn4ZUjjaPVtlP7NyReP21wr7DwYIDmaE6rAP0fMIMbXp9EtZCcoWgXJ9ots9qzc8UqJgyZx1wR6beOc3Quwl/piwrKndOnV+APSMt7BKxyyNkuUktwXzeVuwkVB7RTd0Ct5cuJDCkoxw/0pVz38555WEBH98N51oRVVvB1qEhbM65TeSVGrEafrrcl93ZTAP+w6Go1hXOdZuTY0yB1EhzTCqFFTcZjOIk0e4R+OmGVg2vOloEWu6UCesvXi2YRtWZqsoNNogsZIowBlAPH96pHu4QyHHGaYvxQIDAQABAoIBAGn2BMxceSiDmzqNCrqJPdoT/C8hln4l9f50jEzwfA86rE0ZWRSYBSRCbooPSKrF0GODYExS+KnNHH+BBSrJcySTQHJsBgh0jQ2ZvSEL/joeqcttYfEWqphQ/rReqcsIXQWca0dQppUW48BlVNlPSTGO5lrLAWozBwU0TA9kwKUnRY9FvL+z3v0VZ6O8FaoRHj30omtjl00pubHazNkrfbNkoWKmIntQwHMckFV7t/uf/hjozVLhyiHE5iukTnG7uD/1P4JZ08neMEwPEFFk5OtyziEwBnDhn9MdVW6EJK++B7ztQP5Z1sk/nbYCKk5nAAAgA+LCHp83xs/FVjuGalUCgYEA4T3gyNYKkRkR1WT5APeteXLSqdISeN32s8/p/dfzbS22yMMH/SI76osODeXBeDJayQCA3b1UqpIe3eETffeBHpqSa+yBuWOZhlqPGY4O4Qx6NU4Ofu7E2U+DYe6pv7VaFm4nC5GfTA0v3NEwFg9qy9Tke7KETUO441ZmchKWZRcCgYEAxDkFjKh2+/N1I6HANoUrWhpQc4e4KA4jlZwmjj2ZU2GwGJWmt8ZtLTvQ6f2WxiWDI7smtw/Qd9nJBUbDLw10rqDC28wsp01dKGIHakKRvYBGR/Hu6z62ao15hXi9G9jo63EgnCsSLbQxScMrxHrm3mXRhpaVNxtvqpjieVGmU4MCgYBzvILjT7BQTExwlRipmZqmvAxpPEtHle4tCNmYGL25TPMOB8D0HgIhi2AmXfdilqU1gS+2QJHfr4NyyTNl16aeHhi8oeMqanY5phC/tdIJa4rkFv611GSLsSK2UJcircHjoqgndqUew+vjEA4gV72tjO/2a4010mWPUxcC6HAk8wKBgBJf6JYgXGwBg1Gf3vj8BXA9kJUQ+3y8vjZPVgjx2b7GIhBbSy5gZW7b8BdJSOorLxmUd+6ii3n9qeZVlwd78hY8NaMRfoj4JKYYb+tSaoVdUrd//0iGKdzWfK+z+7l02XLauBwHqH8hJZBrt1iBzca7sCAzT6vGZPGLuYF01OdzAoGAeEoemKZzJgNA+xIVs5DMVhPjBUf/CzPUtb58gW5S8f83Tte0a24YgRbxH3bMmkjMK6cpB4d8Im2Rq+11BVM3AWRrZ0BkIibqijss142cZKJjk2ou32lJwa4AwoMGUbbPvMMCXAo/ZyLZUsPNwuJierGVXSScgoWUYureQohi2r0=',
            'format' => 'json',
            'charset'=>'UTF-8',
            'sign_type'=>"RSA2",
            'alipayrsa_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAkWjkO/R6paFICgxE2PjONklokEPgSYvh2W3JZ1lJjkmCqmfGmupp03wvgLJV+CYnBESWvP1kEkwkQUpmdzDMhVzY3pVVc4aYqjBtk6+Zm87/IP+ykuyX5hxqeaq9MoYi1Pu0NUWYcbAX7/9kAbXOmfvxUOlfOe0tuOXUp9g75+9chme4K8ELBSPn20oyY78jVhozX1xP0B6p/yxOSHZLohF0fqK4RNJBy2E9Wmo5I467TNEGITAXMubFUkYvaPZCD0FsueKOzMv0gbBuvqeN5soAu3U3hi5XFa2DTvaxvVovSmO/v9u/igYmMq1mwll5bYfenYz8PBFAP8odPBeTGwIDAQAB"
        ],
        'wxpay'=>[
            'class'=>'xplwechat\weixin\Wxpay',
            'back_url'=> '/v1/callback/wechat',
            'app_id'=>'wx6f542c8fd815f11e',
            'mch_id'=>'1500662801',
            'key'=>'dc953f4c8c0f5d51eb748bdfe8d8ba64',
            'app_secret'=>'dc953f4c8c0f5d51eb748bdfe8d8ba64',
            'ssl_cert_path'=>'../cert/apiclient_cert.pem',
            'ssl_key_path'=>'../cert/apiclient_key.pem',
            'curl_proxy_host'=>'0.0.0.0',
            'curl_proxy_port'=>0,
            'report_level'=>1,
        ],
        'jpush' => [
            'class' => 'lspbupt\jpush\Jpush',
            'app_key' => "9ddc23d468957b06e24131d6",
            'app_secret' => "e56693b1fe63671d0e1171ee",
        ],
        'openssl' => [
            'class' => 'flmencrypt\encrypt\Openssl',
            'secret' => "tgkdksiweskfla28",
            'iv' => "tgkdksiweskfla28",
        ],
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'user' => [
            'identityClass' => 'api\modules\v1\models\Member',
            'enableSession' => false,
        ],
        'sentry' => [
            'class' => 'mito\sentry\Component',
            'dsn' => 'http://1550ed1708f54489b5fd2c0358b83450@10.240.0.72:9000/4',
            'environment' => 'staging',
            'jsNotifier' => false,
            'jsOptions' => [
                'whitelistUrls' => [

                ],
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
//                [
//                    'class' => 'mito\sentry\Target',
//                    'levels' => ['error','warning'],
//                    'except' => [
//                        'yii\web\HttpException:404',
//                    ],
//                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logFile' => '@app/runtime/logs/error/app.log',
                    'logVars' => [],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info','trace'],
                    'logFile' => '@app/runtime/logs/info/app.log',
                    'logVars' => [],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing'=>false,
            'showScriptName' => false,
            'rules' => $rules,
        ],
    ],
    'params' => $params,
];
