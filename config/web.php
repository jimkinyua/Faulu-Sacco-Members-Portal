<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    // 'layout' => 'register',
    'timeZone' => 'Africa/Nairobi',

    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'timeZone' => 'Africa/Nairobi',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'pPM0Rptc56uwTeYhN-C7CLoczUsskSf4L|II}YHEYHBEFSDGHJFGK&%YRGDZE$%ETSFBDGNFHTUY&',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'authTimeout' => 60 * 60, // 1 Minute ,
            'idParam' => '__cid',
        ],

        'applicant' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\models\NavisionMemberApplication',
            'enableAutoLogin' => false,
            'authTimeout' => 60 * 60, // 1 Minute ,
            // 'loginUrl' => ['application/applicant-login'],
            'idParam' => '__fid',

            'identityCookie' => [
                'name' => '_applicant',
            ]
        ],

        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'navision' => [
            'class' => 'app\Library\Navision',
        ],
        'Mfiles' => [
            'class' => 'app\Library\Mfiles',
        ],
        'MpesaIntergration' => [
            'class' => 'app\Library\MpesaIntergration',
        ],
        'MetroPolIntergration' => [
            'class' => 'app\Library\MetroPolIntergration',
        ],
        'navhelper' => [
            'class' => 'app\Library\Navhelper',
        ],
        'recruitment' => [
            'class' => 'app\Library\Recruitment',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => env('smtpserver'),
                'username' => env('emailusername'),
                'password' => env('emailpassword'),
                'port' => env('port'),
                'encryption' => env('emailencryption'),
                /* 'streamOptions' => [ 'ssl' =>
                    [
                        'allow_self_signed' => true,
                        'verify_peer' => true,
                        'verify_peer_name' => true,
                    ],
                ],*/
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [],
        ],

    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
