<?php
/**
 * Yii2 Dumpling.
 *
 * This file contains base dumper class.
 *
 * @author  Alexei Korotin <herr.offizier@gmail.com>
 */

namespace herroffizier\yii2dumpling\dumpers;

use yii\base\Object;
use yii\base\NotSupportedException;

/**
 * Base dumper class.
 *
 * It just implements DumperInterface but does absolutely nothing useful,
 * so it is just a good point to start from.
 */
abstract class BaseDumper extends Object implements DumperInterface
{
    /**
     * Parsed DSN.
     *
     * @var array
     */
    protected $dsn;

    /**
     * Database username.
     *
     * @var string|null
     */
    protected $username;

    /**
     * Database password.
     *
     * @var string|null
     */
    protected $password;

    public function __construct(array $dsn, $username, $password)
    {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
    }

    public function dump($file)
    {
        // @codeCoverageIgnoreStart
        throw new NotSupportedException(get_called_class().' does not support dumping');
        // @codeCoverageIgnoreEnd
    }

    public function restore($file)
    {
        // @codeCoverageIgnoreStart
        throw new NotSupportedException(get_called_class().' does not support restoring');
        // @codeCoverageIgnoreEnd
    }
}
