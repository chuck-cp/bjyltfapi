<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'pc-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'pc\controllers',
    'bootstrap' => ['log'],
    'components' => [
        'user' => [
            'identityClass' => 'pc\models\Member',
//            'enableAutoLogin' => true,
//            'enableSession' => fal,
        ],
        'cos'=>[
            'class'=>'xplqcloud\cos\Cos',
//            'app_id' => '1255626690',
//            'secret_id' => 'AKIDqfO1Y9xGh2GYu6ewa3LArNm04xfBNhgU',
//            'secret_key' => 'rY4PZYQ8fVAey78zkKjwzvoV1Misr00p',
//            'region' => 'sh',
//            'bucket'=>'yulongchuanmei',
            'app_id' => '1252719796',
            'secret_id' => 'AKID99yd5JE7h280d4mYS7cssfFagWqwsOlv',
            'secret_key' => '4uUqwmMgvwiFZpqc9UaQ1MQEl13pKZaL',
            'region' => 'bj',
            'bucket'=>'yulong',
            'insertOnly'=>true,
            'timeout' => 200
        ],

        'cosB'=>[
            'class'=>'xplqcloud\cos\Cos',
//            'app_id' => '1255626690',
//            'secret_id' => 'AKIDqfO1Y9xGh2GYu6ewa3LArNm04xfBNhgU',
//            'secret_key' => 'rY4PZYQ8fVAey78zkKjwzvoV1Misr00p',
//            'region' => 'sh',
//            'bucket'=>'yulongchuanmei',
            'app_id' => '1252719796',
            'secret_id' => 'AKID99yd5JE7h280d4mYS7cssfFagWqwsOlv',
            'secret_key' => '4uUqwmMgvwiFZpqc9UaQ1MQEl13pKZaL',
            'region' => 'bj',
            'bucket'=>'yulong',
            'insertOnly'=>true,
            'timeout' => 200
        ],
//        'sentry' => [
//            'class' => 'mito\sentry\Component',
//            'dsn' => 'http://a71ad279ddfd4f9483f836d42c7fcfab@10.240.0.72:9000/7',
//            'environment' => 'staging',
//            'jsNotifier' => false,
//            'jsOptions' => [
//                'whitelistUrls' => [
//
//                ],
//            ],
//        ],
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
            'rules' => [
            ]
        ],
        'openssl' => [
            'class' => 'flmencrypt\encrypt\Openssl',
            'secret' => "tgkdksiweskfla28",
            'iv' => "tgkdksiweskfla28",
        ],
    ],
    'params' => $params,
];
