<?php
/**
 * Yii2 Dumpling.
 *
 * This file contains Dumpling module.
 *
 * @author  Alexei Korotin <herr.offizier@gmail.com>
 */

namespace herroffizier\yii2dumpling\commands;

use Yii;
use yii\helpers\Console;

/**
 * Dump or restore databases via console.
 */
class Controller extends \yii\console\Controller
{
    /**
     * Link to module.
     *
     * @var \herroffizier\yii2dumpling\Module
     */
    public $module;

    /**
     * Dump file name.
     *
     * @var string|null
     */
    public $file;

    /**
     * Database component.
     *
     * @var string|null
     */
    public $db;

    /**
     * @param string $actionID
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function options($actionID)
    {
        return ['file', 'db'];
    }

    protected function getDumpFile()
    {
        return Yii::getAlias($this->file ?: $this->module->defaultDumpFile);
    }

    protected function getDbComponent()
    {
        return $this->db ?: $this->module->defaultDbComponent;
    }

    /**
     * Dump database.
     */
    public function actionDump()
    {
        $this->stdout('Dumping ');
        $this->stdout($this->getDbComponent(), Console::BOLD);
        $this->stdout(' to ');
        $this->stdout($this->getDumpFile(), Console::BOLD);
        $this->stdout('... ');

        $this->module->dump($this->file, $this->db);

        $this->stdout('done', Console::BOLD);
        $this->stdout("\n");
    }

    /**
     * Restore database.
     */
    public function actionRestore()
    {
        $this->stdout('Dumping ');
        $this->stdout($this->getDbComponent(), Console::BOLD);
        $this->stdout(' from ');
        $this->stdout($this->getDumpFile(), Console::BOLD);
        $this->stdout('... ');

        $this->module->restore($this->file, $this->db);

        $this->stdout('done', Console::BOLD);
        $this->stdout("\n");
    }
}
