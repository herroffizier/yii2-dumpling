Yii2 Dumpling
=============

[![Build Status](https://travis-ci.org/herroffizier/yii2-dumpling.svg?branch=develop)](https://travis-ci.org/herroffizier/yii2-dumpling) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/herroffizier/yii2-dumpling/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/herroffizier/yii2-dumpling/?branch=develop) [![Code Coverage](https://scrutinizer-ci.com/g/herroffizier/yii2-dumpling/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/herroffizier/yii2-dumpling/?branch=develop)

Yii2 Dumpling is a simple Yii2 extension for dumping and restoring databases.

Installation
------------

Install extension with Composer:

```bash
composer require "herroffizier/yii2-dumpling:@stable"
```

Update your app config:

```php
// Add module to bootstrap
'bootstrap' => [

    // ...

    'dumpling',

    // ...

],

// Add module to app
'modules' => [

    // ...

    'dumpling' => [
        'class' => 'herroffizier\yii2dumpling\Module',
    ],

    // ...

],

```

And you're done.

Usage
-----

Currently only MySQL databases are supported.

To dump database:

```php
Yii::$app->dumpling->dump();
```

To restore database:

```php
Yii::$app->dumpling->restore();
```

By default Dumpling uses ```db``` as database component and ```@app/runtime/dump.sql``` as dump file name. These values may be customized either by module config or by method arguments. Refer to source code for details.

In case of error ```yii\base\Exception``` will be thrown.
