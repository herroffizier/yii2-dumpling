<?php
/**
 * Yii2 Dumpling.
 *
 * This file contains MySQL dumper class.
 *
 * @author  Martin Stolz <herr.offizier@gmail.com>
 */

namespace herroffizier\yii2dumpling\dumpers;

use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * MySQL dumper.
 *
 * Utilizes mysql and mysqldump commands so they should be available on host.
 */
class MysqlDumper extends BaseDumper implements DumperInterface
{
    /**
     * Path to mysqldump binary file.
     *
     * @var string
     */
    public $mysqldumpPath = 'mysqldump';

    /**
     * Path to mysql binary file.
     *
     * @var string
     */
    public $mysqlPath = 'mysql';

    /**
     * Path to store temporary defaults file.
     *
     * @var string
     */
    public $defaultsFilePath = '@app/runtime';

    public function __construct(array $dsn, $username, $password)
    {
        parent::__construct($dsn, $username, $password);

        if (!isset($this->dsn['dbname'])) {
            throw new InvalidConfigException('DSN string does not contain dbname param');
        }
    }

    /**
     * Create defaults file with auth and some other params for MySQL client.
     *
     * Auth params plus all supported options from DSN will be stored this file.
     * File name is randomized.
     *
     * Returns absolute path to file.
     *
     * @return string
     */
    protected function createMysqlDefaultsFile()
    {
        $fileName = Yii::getAlias($this->defaultsFilePath.'/mysql_defaults_'.md5(rand()).'.cnf');

        // @see http://php.net/manual/ru/ref.pdo-mysql.connection.php
        $optionsMap = [
            'user' => 'user',
            'password' => 'password',
            'host' => 'host',
            'port' => 'port',
            'unix_socket' => 'socket',
            'charset' => 'default-character-set',
        ];

        $rawOptions = array_merge($this->dsn, [
            'user' => $this->username,
            'password' => $this->password,
        ]);

        $content = ['[client]'];
        foreach ($rawOptions as $option => $value) {
            // If option from DSN has no mapping name, ignore it
            if (!isset($optionsMap[$option])) {
                continue;
            }
            // If option came with empty value, ignore it
            if (!$value) {
                continue;
            }

            $content[] = $optionsMap[$option].' = '.$value;
        }
        $content = implode("\n", $content);

        file_put_contents($fileName, $content);

        return $fileName;
    }

    protected function runCommand($cmd)
    {
        $process = new Process($cmd);

        $process->mustRun();
    }

    public function dump($file)
    {
        $defaultsFileName = $this->createMysqlDefaultsFile();

        try {
            $this->runCommand(
                escapeshellcmd($this->mysqldumpPath)
                .' --defaults-extra-file='.escapeshellarg($defaultsFileName)
                .' '.escapeshellarg($this->dsn['dbname'])
                .' > '.escapeshellarg($file)
            );
        } catch (ProcessFailedException $e) {
            unlink($defaultsFileName);

            throw new Exception('mysqldump failed: '.$e->getProcess()->getErrorOutput());
        }
    }

    public function restore($file)
    {
        $defaultsFileName = $this->createMysqlDefaultsFile();

        try {
            $this->runCommand(
                escapeshellcmd($this->mysqlPath)
                .' --defaults-extra-file='.escapeshellarg($defaultsFileName)
                .' '.escapeshellarg($this->dsn['dbname'])
                .' < '.escapeshellarg($file)
            );
        } catch (ProcessFailedException $e) {
            unlink($defaultsFileName);

            throw new Exception('mysql failed: '.$e->getProcess()->getErrorOutput());
        }
    }
}
