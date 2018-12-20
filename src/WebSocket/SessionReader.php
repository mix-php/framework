<?php

namespace Mix\WebSocket;

use Mix\Core\Component;

/**
 * SessionReader组件
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class SessionReader extends Component
{

    /**
     * 处理者
     * @var \Mix\Redis\RedisConnection
     */
    public $handler;

    // Key前缀
    public $keyPrefix = 'SESSION:';

    // session名
    public $name = 'session_id';

    // SessionKey
    protected $_sessionKey;

    // SessionID
    protected $_sessionId;

    // 载入session_id
    public function loadSessionId($request)
    {
        // 关闭
        $this->close();
        // 载入session_id
        $this->_sessionId = $request->get($this->name) or
        $this->_sessionId = $request->cookie($this->name);
        $this->_sessionKey = $this->keyPrefix . $this->_sessionId;
        // 返回
        return $this;
    }

    // 关闭
    public function close()
    {
        // 关闭连接
        $this->handler->disconnect();
    }

    // 取值
    public function get($name = null)
    {
        if (is_null($name)) {
            $array = $this->handler->hGetAll($this->_sessionKey);
            foreach ($array as $key => $item) {
                $array[$key] = unserialize($item);
            }
            return $array ?: [];
        }
        $reslut = $this->handler->hmGet($this->_sessionKey, [$name]);
        $value  = array_shift($reslut);
        return $value === false ? null : unserialize($value);
    }

    // 判断是否存在
    public function has($name)
    {
        $exist = $this->handler->hExists($this->_sessionKey, $name);
        return $exist ? true : false;
    }

    // 获取SessionId
    public function getSessionId()
    {
        return $this->_sessionId;
    }

}
