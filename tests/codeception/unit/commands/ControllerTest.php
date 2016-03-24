<?php
/**
 * Yii2 Dumpling.
 *
 * This file contains console controller test.
 *
 * @author  Alexei Korotin <herr.offizier@gmail.com>
 */

namespace herroffizier\yii2dumpling\tests\codeception\unit\commands;

use Yii;
use herroffizier\yii2dumpling\tests\codeception\unit\TestCase;
use Codeception\Util\Stub;

class ControllerTest extends TestCase
{
    public function testConfig()
    {
        $this->specify('dumpling/dump namespace correcly resolved', function () {
            $controller = Yii::$app->createController('dumpling/dump');

            $this->assertArrayHasKey(0, $controller);

            $this->assertInstanceOf(
                'herroffizier\yii2dumpling\commands\Controller',
                $controller[0]
            );
        });

        $this->specify('dumpling/restore namespace correcly resolved', function () {
            $controller = Yii::$app->createController('dumpling/restore');

            $this->assertArrayHasKey(0, $controller);

            $this->assertInstanceOf(
                'herroffizier\yii2dumpling\commands\Controller',
                $controller[0]
            );
        });
    }

    public function testDump()
    {
        $this->specify('console dump command worked correctly', function () {
            $dumpFilePath = Yii::getAlias(Yii::$app->dumpling->defaultDumpFile);

            $this->assertFileNotExists($dumpFilePath, 'dump file already exists');

            $output = '';
            list($controller) = Yii::$app->createController('dumpling/dump');
            $controller = Stub::construct(
                $controller,
                [$controller->id, $controller->module],
                [
                    'stdout' => function ($string) use (&$output) {
                        $output .= $string;
                    },
                ]
            );

            $controller->actionDump();

            $this->assertFileExists($dumpFilePath, 'dump file not created');
            $this->assertContains($dumpFilePath, $output, 'dump file name is not displayed');
            $this->assertContains(
                Yii::$app->dumpling->defaultDbComponent,
                $output,
                'db component name is not displayed'
            );
            $this->assertContains('done', $output, 'done marker is not displayed');
        });
    }

    public function testRestore()
    {
        $this->specify('console restore command worked correctly', function () {
            $dumpFilePath = Yii::getAlias('@app/codeception/_data/dump_data.sql');

            $output = '';
            list($controller) = Yii::$app->createController('dumpling/restore');
            $controller = Stub::construct(
                $controller,
                [$controller->id, $controller->module, ['file' => $dumpFilePath]],
                [
                    'stdout' => function ($string) use (&$output) {
                        $output .= $string;
                    },
                ]
            );

            $controller->actionRestore();

            $this->assertContains($dumpFilePath, $output, 'dump file name is not displayed');
            $this->assertContains(
                Yii::$app->dumpling->defaultDbComponent,
                $output,
                'db component name is not displayed'
            );
            $this->assertContains('done', $output, 'done marker is not displayed');
        });
    }
}
