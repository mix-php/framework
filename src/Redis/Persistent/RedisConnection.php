<?php

namespace Mix\Redis\Persistent;

/**
 * redis长连接组件
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class RedisConnection extends BaseRedisConnection
{

    // 析构事件
    public function onDestruct()
    {
        parent::onDestruct();
        // 关闭连接
        $this->disconnect();
    }

}
