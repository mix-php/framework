<?php

namespace Mix\Http;

use Mix\Core\Component;
use Mix\Helpers\RandomStringHelper;

/**
 * Token组件
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class Token extends Component
{

    /**
     * 连接池
     * @var \Mix\Pool\ConnectionPoolInterface
     */
    public $pool;

    /**
     * 处理者
     * @var \Mix\Redis\RedisConnection
     */
    public $handler;

    // Key前缀
    public $keyPrefix = 'TOKEN:';

    // 有效期
    public $expiresIn = 604800;

    // session名
    public $name = 'access_token';

    // TokenKey
    protected $_tokenKey;

    // TokenID
    protected $_tokenId;

    // TokenID长度
    protected $_tokenIdLength = 32;

    // 请求前置事件
    public function onRequestBefore()
    {
        parent::onRequestBefore();
        // 从连接池获取连接
        if (isset($this->pool)) {
            $this->handler = $this->pool->getConnection();
        }
        // 载入TokenID
        $this->loadTokenId();
    }

    // 载入TokenID
    public function loadTokenId()
    {
        $this->_tokenId = \Mix::$app->request->get($this->name) or
        $this->_tokenId = \Mix::$app->request->header($this->name) or
        $this->_tokenId = \Mix::$app->request->post($this->name);
        $this->_tokenKey = $this->keyPrefix . $this->_tokenId;
    }

    // 创建TokenID
    public function createTokenId()
    {
        do {
            $this->_tokenId  = RandomStringHelper::randomAlphanumeric($this->_tokenIdLength);
            $this->_tokenKey = $this->keyPrefix . $this->_tokenId;
        } while ($this->handler->exists($this->_tokenKey));
    }

    // 设置唯一索引
    public function setUniqueIndex($uniqueId, $uniqueIndexPrefix = 'client_credentials:')
    {
        $uniqueKey = $this->keyPrefix . $uniqueIndexPrefix . $uniqueId;
        // 删除旧token数据
        $oldTokenId = $this->handler->get($uniqueKey);
        if (!empty($oldTokenId)) {
            $oldTokenkey = $this->keyPrefix . $oldTokenId;
            $this->handler->del($oldTokenkey);
        }
        // 更新唯一索引
        $this->handler->setex($uniqueKey, $this->expiresIn, $this->_tokenId);
        // 在数据中加入索引信息
        $this->handler->hmset($this->_tokenKey, ['__uidx__' => $uniqueId]);
    }

    // 赋值
    public function set($name, $value)
    {
        $success = $this->handler->hmset($this->_tokenKey, [$name => serialize($value)]);
        $this->handler->expire($this->_tokenKey, $this->expiresIn);
        return $success ? true : false;
    }

    // 取值
    public function get($name = null)
    {
        if (is_null($name)) {
            $result = $this->handler->hgetall($this->_tokenKey);
            unset($result['__uidx__']);
            foreach ($result as $key => $item) {
                $result[$key] = unserialize($item);
            }
            return $result ?: [];
        }
        $value = $this->handler->hget($this->_tokenKey, $name);
        return $value === false ? null : unserialize($value);
    }

    // 判断是否存在
    public function has($name)
    {
        $exist = $this->handler->hexists($this->_tokenKey, $name);
        return $exist ? true : false;
    }

    // 删除
    public function delete($name)
    {
        $success = $this->handler->hdel($this->_tokenKey, $name);
        return $success ? true : false;
    }

    // 清除token
    public function clear()
    {
        $success = $this->handler->del($this->_tokenKey);
        return $success ? true : false;
    }

    // 获取TokenId
    public function getTokenId()
    {
        return $this->_tokenId;
    }

    // 刷新token
    public function refresh($uniqueIndexPrefix = 'client_credentials:')
    {
        // 判断 token 是否存在
        $tokenData = $this->handler->hgetall($this->_tokenKey);
        if (empty($tokenData)) {
            return false;
        }
        // 定义变量
        $oldData     = $tokenData;
        $oldTokenKey = $this->_tokenKey;
        $newTokenId  = RandomStringHelper::randomAlphanumeric($this->_tokenIdLength);
        $newTokenKey = $this->keyPrefix . $newTokenId;
        $uniqueKey   = $this->keyPrefix . $uniqueIndexPrefix . $oldData['__uidx__'];
        // 判断索引是否正确
        $exists = $this->handler->exists($uniqueKey);
        if (empty($exists)) {
            return false;
        }
        // 删除旧数据
        $this->handler->del($oldTokenKey);
        // 生成新数据
        $this->handler->hmset($newTokenKey, $oldData);
        $this->handler->expire($newTokenKey, $this->expiresIn);
        // 更新索引信息
        $this->handler->set($uniqueKey, $newTokenId);
        $this->handler->expire($uniqueKey, $this->expiresIn);
        // 新 token 赋值到属性
        $this->_tokenId  = $newTokenId;
        $this->_tokenKey = $newTokenKey;
        // 返回
        return true;
    }

}
