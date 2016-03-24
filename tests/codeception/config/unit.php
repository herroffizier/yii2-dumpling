<?php

return [
    'id' => 'test',
    'basePath' => __DIR__.'/../../',
    'class' => 'yii\console\Application',
    'bootstrap' => [
        'dumpling',
    ],
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=test',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],
        'unsupportedDb' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'sqlite::memory:',
        ],
    ],
    'modules' => [
        'dumpling' => [
            'class' => 'herroffizier\yii2dumpling\Module',
        ],
    ],
];
