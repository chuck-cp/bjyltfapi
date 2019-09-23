<?php

$vendorDir = dirname(__DIR__);

return array (
  'yiisoft/yii2-redis' => 
  array (
    'name' => 'yiisoft/yii2-redis',
    'version' => '2.0.4.0',
    'alias' => 
    array (
      '@yii/redis' => $vendorDir . '/yiisoft/yii2-redis',
    ),
  ),
  'yiisoft/yii2-gii' => 
  array (
    'name' => 'yiisoft/yii2-gii',
    'version' => '2.0.5.0',
    'alias' => 
    array (
      '@yii/gii' => $vendorDir . '/yiisoft/yii2-gii',
    ),
  ),
'yiisoft/yii2-mongodb' =>
    array (
        'name' => 'yiisoft/yii2-mongodb',
        'version' => '2.1.4.0',
        'alias' =>
            array (
                '@yii/mongodb' => $vendorDir . '/yiisoft/yii2-mongodb',
            ),
    ),
  '2amigos/yii2-qrcode-helper' => 
  array (
    'name' => '2amigos/yii2-qrcode-helper',
    'version' => '1.0.3.0',
    'alias' => 
    array (
      '@dosamigos/qrcode' => $vendorDir . '/2amigos/yii2-qrcode-helper/src',
    ),
  ),
  'xplqcloud/cos' => 
  array (
    'name' => 'xplqcloud/cos',
    'version' => '0.1.2.0',
    'alias' => 
    array (
      '@xplqcloud/cos' => $vendorDir . '/xplqcloud/cos/src',
    ),
  ),
  'xplwechat/weixin' => 
  array (
    'name' => 'xplwechat/weixin',
    'version' => '0.1.0.0',
    'alias' => 
    array (
      '@xplwechat/weixin' => $vendorDir . '/xplwechat/weixin/src',
    ),
  ),
  'flmencrypt/encrypt' => 
  array (
    'name' => 'flmencrypt/encrypt',
    'version' => '1.0.0.0',
    'alias' => 
    array (
      '@flmencrypt/encrypt' => $vendorDir . '/flmencrypt/encrypt/src',
    ),
  ),
  'yiisoft/yii2-bootstrap' => 
  array (
    'name' => 'yiisoft/yii2-bootstrap',
    'version' => '2.0.8.0',
    'alias' => 
    array (
      '@yii/bootstrap' => $vendorDir . '/yiisoft/yii2-bootstrap/src',
    ),
  ),
  'yiisoft/yii2-debug' => 
  array (
    'name' => 'yiisoft/yii2-debug',
    'version' => '2.0.12.0',
    'alias' => 
    array (
      '@yii/debug' => $vendorDir . '/yiisoft/yii2-debug',
    ),
  ),
  'xplalipay/alipay' => 
  array (
    'name' => 'xplalipay/alipay',
    'version' => '0.1.0.0',
    'alias' => 
    array (
      '@xplalipay/alipay' => $vendorDir . '/xplalipay/alipay/src',
    ),
  ),
  'mito/yii2-sentry' => 
  array (
    'name' => 'mito/yii2-sentry',
    'version' => '1.0.4.0',
    'alias' => 
    array (
      '@mito/sentry' => $vendorDir . '/mito/yii2-sentry/src',
      '@mito/sentry/tests' => $vendorDir . '/mito/yii2-sentry/tests',
    ),
  ),
);
