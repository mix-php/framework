<?php

namespace mix\client;

/**
 * redis组件
 * @author 刘健 <coder.liu@qq.com>
 *
 * @method select($index)
 * @method set($key, $value)
 * @method setex($key, $seconds, $value)
 * @method setnx($key, $value)
 * @method get($key)
 * @method del($key)
 * @method hmset($key, $array)
 * @method hmget($key, $array)
 * @method hgetall($key)
 * @method hlen($key)
 * @method hset($key, $field, $value)
 * @method hsetnx($key, $field, $value)
 * @method hget($key, $field)
 * @method lpush($key, $value)
 * @method rpop($key)
 * @method brpop($key, $timeout)
 * @method rpush($key, $value)
 * @method lpop($key)
 * @method blpop($key, $timeout)
 * @method sadd($key, $value)
 * @method lrange($key, $start, $end)
 * @method llen($key)
 * @method subscribe($channel)
 * @method publish($channel, $message)
 * @method ttl($key)
 */
class Redis extends BaseRedis
{

    // 请求后置事件
    public function onRequestAfter()
    {
        parent::onRequestAfter();
        // 关闭连接
        $this->disconnect();
    }

    // 析构事件
    public function onDestruct()
    {
        parent::onDestruct();
        // 关闭连接
        $this->disconnect();
    }

}
