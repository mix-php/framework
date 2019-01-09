<?php

namespace Mix\Pool;

/**
 * Interface ConnectionPoolInterface
 * @author LIUJIAN <coder.keda@gmail.com>
 * @package Mix\Pool
 */
interface ConnectionPoolInterface
{

    /**
     * 创建连接
     * @return mixed
     */
    public function createConnection();

    /**
     * 获取连接
     * @return mixed
     */
    public function getConnection();

    /**
     * 释放连接
     * @param $connection
     */
    public function release($connection);

    /**
     * 获取连接池的统计信息
     * @return array
     */
    public function getStats();

}
