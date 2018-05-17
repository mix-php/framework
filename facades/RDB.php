<?php

namespace mix\facades;

use mix\base\Facade;

/**
 * RDB 门面类
 * @author 刘健 <coder.liu@qq.com>
 *
 * @method disconnect() static
 * @method queryBuilder($sqlItem) static
 * @method createCommand($sql = null) static
 * @method bindParams($data) static
 * @method queryAll() static
 * @method queryOne() static
 * @method queryColumn($columnNumber = 0) static
 * @method queryScalar() static
 * @method execute() static
 * @method getLastInsertId() static
 * @method getRowCount() static
 * @method insert($table, $data) static
 * @method batchInsert($table, $data) static
 * @method update($table, $data, $where) static
 * @method delete($table, $where) static
 * @method transaction($closure) static
 * @method beginTransaction() static
 * @method commit() static
 * @method rollback() static
 * @method getRawSql() static
 */
class RDB extends Facade
{

    // 获取实例
    public static function getInstance()
    {
        return \Mix::app()->rdb;
    }

}
