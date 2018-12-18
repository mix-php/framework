<?php

namespace Mix\WebSocket;

use Mix\Core\Component;

/**
 * TokenReader组件
 * @author 刘健 <coder.liu@qq.com>
 */
class TokenReader extends Component
{

    /**
     * 处理者
     * @var \Mix\Redis\RedisConnection
     */
    public $handler;

    // Key前缀
    public $keyPrefix = 'TOKEN:';

    // session名
    public $name = 'access_token';

    // TokenKey
    protected $_tokenKey;

    // TokenID
    protected $_tokenId;

    // Token前缀
    protected $_tokenPrefix;

    // 初始化事件
    public function onInitialize()
    {
        parent::onInitialize();
        // 前缀处理
        $this->_tokenPrefix = $this->keyPrefix;
    }

    // 载入TokenID
    public function loadTokenId($request)
    {
        // 关闭
        $this->close();
        // 载入TokenID
        $this->_tokenId = $request->get($this->name) or
        $this->_tokenId = $request->header($this->name);
        $this->_tokenKey = $this->_tokenPrefix . $this->_tokenId;
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
            $array = $this->handler->hGetAll($this->_tokenKey);
            foreach ($array as $key => $item) {
                $array[$key] = unserialize($item);
            }
            return $array ?: [];
        }
        $reslut = $this->handler->hmGet($this->_tokenKey, [$name]);
        $value  = array_shift($reslut);
        return $value === false ? null : unserialize($value);
    }

    // 判断是否存在
    public function has($name)
    {
        $exist = $this->handler->hExists($this->_tokenKey, $name);
        return $exist ? true : false;
    }

    // 获取TokenId
    public function getTokenId()
    {
        return $this->_tokenId;
    }

}
