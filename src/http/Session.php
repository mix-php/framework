<?php

namespace mix\http;

use mix\base\Component;
use mix\helpers\StringHelper;

/**
 * Session组件
 * @author 刘健 <coder.liu@qq.com>
 */
class Session extends Component
{

    // 保存处理者
    public $saveHandler;

    // 保存的Key前缀
    public $saveKeyPrefix;

    // 有效期
    public $expires = 7200;

    // session名
    public $name = 'mixssid';

    // SessionKey
    protected $_sessionKey;

    // SessionID
    protected $_sessionId;

    // 请求前置事件
    public function onRequestBefore()
    {
        parent::onRequestBefore();
        // 载入session_id
        $this->loadSessionId();
    }

    // 请求后置事件
    public function onRequestAfter()
    {
        parent::onRequestAfter();
        // 关闭连接
        $this->saveHandler->disconnect();
    }

    // 载入session_id
    public function loadSessionId()
    {
        $this->_sessionId = \Mix::app()->request->cookie($this->name);
        if (is_null($this->_sessionId)) {
            // 创建session_id
            $this->_sessionId = StringHelper::getRandomString(26);
        }
        $this->_sessionKey = $this->saveKeyPrefix . $this->_sessionId;
        $this->saveHandler->expire($this->_sessionKey, $this->expires);
    }

    // 赋值
    public function set($name, $value)
    {
        $success = $this->saveHandler->hmset($this->_sessionKey, [$name => serialize($value)]);
        $this->saveHandler->expire($this->_sessionKey, $this->expires);
        \Mix::app()->response->setCookie($this->name, $this->_sessionId, 0, '/');
        return $success ? true : false;
    }

    // 取值
    public function get($name = null)
    {
        if (is_null($name)) {
            $result = $this->saveHandler->hgetall($this->_sessionKey);
            foreach ($result as $key => $item) {
                $result[$key] = unserialize($item);
            }
            return $result ?: [];
        }
        $value = $this->saveHandler->hget($this->_sessionKey, $name);
        return $value === false ? null : unserialize($value);
    }

    // 判断是否存在
    public function has($name)
    {
        $exist = $this->saveHandler->hexists($this->_sessionKey, $name);
        return $exist ? true : false;
    }

    // 删除
    public function delete($name)
    {
        $success = $this->saveHandler->hdel($this->_sessionKey, $name);
        return $success ? true : false;
    }

    // 清除session
    public function clear()
    {
        $success = $this->saveHandler->del($this->_sessionKey);
        return $success ? true : false;
    }

    // 获取SessionId
    public function getSessionId()
    {
        return $this->_sessionId;
    }

}
