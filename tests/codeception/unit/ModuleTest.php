<?php
/**
 * Yii2 Dumpling.
 *
 * This file contains Dumpling component test.
 *
 * @author  Martin Stolz <herr.offizier@gmail.com>
 */

namespace herroffizier\yii2dumpling\tests\codeception\unit;

use Yii;

class ModuleTest extends TestCase
{
    public function testBootstrap()
    {
        $this->specify('module loaded', function () {
            $this->assertInstanceOf(
                'herroffizier\yii2dumpling\Module',
                Yii::$app->dumpling,
                'module is not an herroffizier\yii2dumpling\Module instance'
            );
        });
    }

    public function testDump()
    {
        $this->specify('db dumped', function () {
            $dumpFilePath = Yii::getAlias(Yii::$app->dumpling->defaultDumpFile);

            $this->assertFileNotExists($dumpFilePath, 'dump file already exists');
            Yii::$app->dumpling->dump();
            $this->assertFileExists($dumpFilePath, 'dump file is not created');
            $this->assertContains('test', file_get_contents($dumpFilePath), 'dump file have no tables in it');
        });
    }

    public function testRestore()
    {
        $this->specify('db restored', function () {
            $dumpFilePath = Yii::getAlias('@app/codeception/_data/dump_data.sql');

            $this->assertFileExists($dumpFilePath, 'predefined dump file is missing');
            $this->assertEquals(0, (int) (new \yii\db\Query())->from('test')->count(), 'test table is not empty');
            Yii::$app->dumpling->restore($dumpFilePath);
            $this->assertEquals(3, (int) (new \yii\db\Query())->from('test')->count(), 'test table is not restored');
        });
    }

    public function testEdgeCases()
    {
        $this->specify('db is not dumped with invalid db component', function () {
            $dumpFilePath = Yii::getAlias(Yii::$app->dumpling->defaultDumpFile);

            Yii::$app->dumpling->dump($dumpFilePath, 'dumpling');
        }, [
            'throws' => 'yii\base\InvalidParamException',
        ]);

        $this->specify('db is not dumped with unsupported db component', function () {
            $dumpFilePath = Yii::getAlias(Yii::$app->dumpling->defaultDumpFile);

            Yii::$app->dumpling->dump($dumpFilePath, 'unsupportedDb');
        }, [
            'throws' => 'yii\base\NotSupportedException',
        ]);
    }
}
