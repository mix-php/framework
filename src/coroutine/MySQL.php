<?php

namespace mix\coroutine;

use mix\base\Component;

/**
 * MySQL组件
 * @author 刘健 <coder.liu@qq.com>
 */
class MySQL extends Component
{

    // 主机
    public $host = '';

    // 端口
    public $port = '';

    // 数据库
    public $database = '';

    // 密码
    public $password = '';

    /**
     * 连接池
     * @var \mix\coroutine\PoolManager
     */
    public $pool;

    /**
     * redis对象
     * @var \Swoole\Coroutine\Redis
     */
    protected $_mysql;


    // 析构事件
    public function onDestruct()
    {
        parent::onDestruct();
        // 关闭连接
        $this->disconnect();
    }

    // 创建连接
    protected function createConnection()
    {
        $this->pool->activeCountIncrement();
        $mysql   = new \Swoole\Coroutine\MySQL();
        $success = $mysql->connect([
            'host'     => '192.168.1.200',
            'port'     => 3306,
            'user'     => 'root',
            'password' => '123456',
            'database' => 'test',
        ]);
        if (!$success) {
            $this->pool->activeCountDecrement();
            throw new \mix\exceptions\ConnectionException('mysql connection failed');
        }
        return $mysql;
    }

    // 获取连接
    protected function getConnection()
    {
        if ($this->pool->getQueueCount() > 0) {
            var_dump('getQueueCount > 0');
            return $this->pool->pop();
        }
        if ($this->pool->getCurrentCount() >= $this->pool->max) {
            var_dump('getCurrentCount >= max');
            return $this->pool->pop();
        }
        var_dump('createConnection');
        return $this->createConnection();
    }

    // 连接
    protected function connect()
    {
        $this->_mysql = $this->getConnection();
    }

    // 关闭连接
    public function disconnect()
    {
        if (isset($this->_mysql)) {
            $this->pool->push($this->_mysql);
            $this->_mysql = null;
        }
    }

    // 自动连接
    protected function autoConnect()
    {
        if (!isset($this->_mysql)) {
            $this->connect();
        }
    }

    // 执行命令
    public function __call($name, $arguments)
    {
        // 自动连接
        $this->autoConnect();
        // 执行命令
        $res = $this->_mysql->query('select sleep(1)');
    }

}
