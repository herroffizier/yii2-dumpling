<?php
/**
 * Yii2 Dumpling.
 *
 * This file contains dumper interface.
 *
 * @author  Alexei Korotin <herr.offizier@gmail.com>
 */

namespace herroffizier\yii2dumpling\dumpers;

/**
 * Dumper interface.
 *
 * All dumpers must implement this interface.
 */
interface DumperInterface
{
    /**
     * Constructor.
     *
     * If username or password are not supported, nulls should be passed.
     *
     * @param array       $dsn
     * @param string|null $username
     * @param string|null $password
     */
    public function __construct(array $dsn, $username, $password);

    /**
     * Dump database to given file.
     *
     * Aliases in file name should not be used as they should be resolved earlier.
     *
     * @param string $file
     */
    public function dump($file);

    /**
     * Restore database from given file.
     *
     * Aliases in file name should not be used as they should be resolved earlier.
     *
     * @param string $file
     */
    public function restore($file);
}
