<?php

namespace Mix\Facades;

use Mix\Base\Facade;

/**
 * RDB 门面类
 * @author 刘健 <coder.liu@qq.com>
 *
 * @method disconnect() static
 * @method \Mix\Client\PDO queryBuilder($sqlItem) static
 * @method \Mix\Client\PDO createCommand($sql = null) static
 * @method \Mix\Client\PDO bindParams($data) static
 * @method queryAll() static
 * @method queryOne() static
 * @method queryColumn($columnNumber = 0) static
 * @method queryScalar() static
 * @method execute() static
 * @method getLastInsertId() static
 * @method getRowCount() static
 * @method \Mix\Client\PDO insert($table, $data) static
 * @method \Mix\Client\PDO batchInsert($table, $data) static
 * @method \Mix\Client\PDO update($table, $data, $where) static
 * @method \Mix\Client\PDO delete($table, $where) static
 * @method transaction($closure) static
 * @method beginTransaction() static
 * @method commit() static
 * @method rollback() static
 * @method getRawSql() static
 */
class PDO extends Facade
{

    /**
     * 获取实例
     * @param $name
     * @return \Mix\Client\PDO
     */
    public static function name($name)
    {
        return static::getInstances()[$name];
    }

    /**
     * 获取实例集合
     * @return array
     */
    public static function getInstances()
    {
        return [
            'default' => \Mix::app()->pdo,
        ];
    }

}
