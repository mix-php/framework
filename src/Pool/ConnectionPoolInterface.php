<?php

namespace Mix\Pool;

/**
 * Interface ConnectionPoolInterface
 * @author LIUJIAN <coder.keda@gmail.com>
 */
interface ConnectionPoolInterface
{

    // 创建连接
    public function createConnection();

    // 获取连接
    public function getConnection();

    // 释放连接
    public function release($connection);

    // 获取连接池的统计信息
    public function getStats();

}
