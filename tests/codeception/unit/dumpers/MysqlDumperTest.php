<?php
/**
 * Yii2 Dumpling.
 *
 * This file contains MySQL dumper test.
 *
 * @author  Martin Stolz <herr.offizier@gmail.com>
 */

namespace herroffizier\yii2dumpling\tests\codeception\unit;

use Yii;
use herroffizier\yii2dumpling\dumpers\MysqlDumper;

class MysqlDumperTest extends TestCase
{
    public function testConstructor()
    {
        $this->specify('MySQL dumper is successfully initialized', function () {
            $dumper = new MysqlDumper(['dbname' => 'test'], 'root', '');
            $this->assertInstanceOf(
                'herroffizier\yii2dumpling\dumpers\MysqlDumper',
                $dumper,
                'module is not an herroffizier\yii2dumpling\dumpers\MysqlDumper instance'
            );
        });
    }

    public function testEdgeCases()
    {
        $this->specify('MySQL dumper failed to initialize with incorrect DSN', function () {
            new MysqlDumper([], 'root', '');
        }, [
            'throws' => 'yii\base\InvalidConfigException',
        ]);

        $this->specify('MySQL dumper failed to dump with incorrect MySQL auth', function () {
            $dumper = new MysqlDumper(['dbname' => 'test2'], 'root', '');
            $dumper->dump(Yii::getAlias('@app/runtime/dump.sql'));
        }, [
            'throws' => 'yii\base\Exception',
        ]);

        $this->specify('MySQL dumper failed to restore with incorrect MySQL auth', function () {
            $dumper = new MysqlDumper(['dbname' => 'test2'], 'root', '');
            $dumper->restore(Yii::getAlias('@app/runtime/dump.sql'));
        }, [
            'throws' => 'yii\base\Exception',
        ]);
    }
}
