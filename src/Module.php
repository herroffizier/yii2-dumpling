<?php
/**
 * Yii2 Dumpling.
 *
 * This file contains Dumpling module.
 *
 * @author  Martin Stolz <herr.offizier@gmail.com>
 */

namespace herroffizier\yii2dumpling;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\InvalidParamException;
use yii\base\NotSupportedException;
use yii\db\Connection;

/**
 * Dumpling module.
 *
 * Acts like a facade for actual dumpers.
 */
class Module extends \yii\base\Module implements BootstrapInterface
{
    /**
     * Default database component.
     *
     * @var string
     */
    public $defaultDbComponent = 'db';

    /**
     * Default name for dump file.
     *
     * Aliases supported.
     *
     * @var string
     */
    public $defaultDumpFile = '@app/runtime/dump.sql';

    /**
     * Array of dumpers for different databases.
     *
     * Keys must be a valid DSN prefix.
     *
     * Values must be either a string which represents dumper class name
     * or an array with 'class' field.
     * Values are passed to Yii::createObject() method.
     *
     * @var mixed
     */
    public $dumpers = [
        'mysql' => [
            'class' => 'herroffizier\yii2dumpling\dumpers\MysqlDumper',
        ],
    ];

    /**
     * @param mixed $app
     * @codeCoverageIgnore
     */
    public function bootstrap($app)
    {
        $app->set($this->id, $this);
    }

    /**
     * Parse DSN string into array.
     *
     * @param string $dsn
     *
     * @return string[]
     */
    protected function parseDsn($dsn)
    {
        list($driverName, $dsn) = explode(':', $dsn, 2);

        $values = ['driverName' => $driverName];
        $options = explode(';', $dsn);
        foreach ($options as $option) {
            // Some options in DSN may be a single string.
            if (mb_strpos($option, '=') !== false) {
                list($key, $value) = explode('=', $option, 2);
                $values[$key] = $value;
            } else {
                $values[] = $option;
            }
        }

        return $values;
    }

    protected function createDumper(Connection $dbComponent)
    {
        $dsn = $this->parseDsn($dbComponent->dsn);

        if (!isset($this->dumpers[$dsn['driverName']])) {
            throw new NotSupportedException('Cannot handle '.$dsn['driverName'].' db');
        }

        $dumper = Yii::createObject(
            $this->dumpers[$dsn['driverName']],
            [
                $dsn,
                $dbComponent->username,
                $dbComponent->password,
            ]
        );

        return $dumper;
    }

    protected function getDbComponent($db)
    {
        if (!$db) {
            $db = $this->defaultDbComponent;
        }

        $component = Yii::$app->{$db};

        if (!($component instanceof Connection)) {
            throw new InvalidParamException($db.' is not an '.Connection::className().' instance');
        }

        return $component;
    }

    protected function getDumpFilePath($file)
    {
        if (!$file) {
            $file = $this->defaultDumpFile;
        }

        return Yii::getAlias($file);
    }

    /**
     * Dump database to file.
     *
     * If file or db params are not specified, default values will be used.
     *
     * @param string $file
     * @param string $db
     */
    public function dump($file = null, $db = null)
    {
        $dbComponent = $this->getDbComponent($db);
        $filePath = $this->getDumpFilePath($file);

        $dumper = $this->createDumper($dbComponent);

        $dumper->dump($filePath);
    }

    /**
     * Restore database from file.
     *
     * If file or db params are not specified, default values will be used.
     *
     * @param string $file
     * @param string $db
     */
    public function restore($file = null, $db = null)
    {
        $dbComponent = $this->getDbComponent($db);
        $filePath = $this->getDumpFilePath($file);

        $dumper = $this->createDumper($dbComponent);

        $dumper->restore($filePath);
    }
}
