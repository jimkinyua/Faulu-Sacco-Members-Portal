<?php

return [

    'class' => 'yii\db\Connection',
    'driverName' => 'sqlsrv',
    'dsn' => 'sqlsrv:server='.env('DB_SERVER').';database='.env('DatabaseName'),
    'username' => env('DB_USER'),
    'password' => env('DB_PASSWORD'),
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
