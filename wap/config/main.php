<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'wap-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'wap\controllers',
    'defaultRoute'=>'index',
    'bootstrap' => ['log'],
    'components' => [
//        'errorHandler'=>[
//            'errorAction'=>'index/error',
//        ],
        'user' => [
            'identityClass' => 'wap\models\Member',
            'enableSession' => false,
        ],
//        'sentry' => [
//            'class' => 'mito\sentry\Component',
//            'dsn' => 'http://63c0d264b95f46f480830c05103c4c0a@10.240.0.72:9000/6',
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
                //查看用户信息
                'GET agreement/<id>'=>'agreement/view',
                //查看用户消息
                'GET message/<id>'=>'message/view',
                //app查看协议
                'GET appagreement/<id>'=>'agreement/appview',

            ]
        ],
    ],
    'params' => $params,
];
