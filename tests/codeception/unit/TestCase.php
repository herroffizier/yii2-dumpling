<?php
/**
 * Yii2 Dumpling.
 *
 * This file contains base test case class.
 *
 * @author  Alexei Korotin <herr.offizier@gmail.com>
 */

namespace herroffizier\yii2dumpling\tests\codeception\unit;

use Yii;
use Codeception\Specify;

abstract class TestCase extends \yii\codeception\TestCase
{
    use Specify;

    protected function setUp()
    {
        parent::setUp();

        $this->specifyConfig()->deepClone(false);

        exec('rm -rf '.Yii::getAlias('@app/runtime').'/*');
    }
}
