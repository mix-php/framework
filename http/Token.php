<?php

namespace mix\http;

use mix\base\Component;
use mix\helpers\StringHelper;

/**
 * Token组件
 * @author 刘健 <coder.liu@qq.com>
 */
class Token extends Component
{

    // 保存处理者
    public $saveHandler;

    // 保存的Key前缀
    public $saveKeyPrefix;

    // 有效期
    public $expires = 7200;

    // session名
    public $name = 'access_token';

    // TokenKey
    protected $_tokenKey;

    // TokenID
    protected $_tokenId;

    // Token前缀
    protected $_tokenPrefix;

    // 唯一索引前缀
    protected $_uniqueIndexPrefix;

    // 初始化事件
    public function onInitialize()
    {
        parent::onInitialize();
        // 前缀处理
        $this->_tokenPrefix       = $this->saveKeyPrefix . 'DATA:';
        $this->_uniqueIndexPrefix = $this->saveKeyPrefix . 'UIDX:';
    }

    // 请求开始事件
    public function onRequestStart()
    {
        parent::onRequestStart();
        // 载入TokenID
        $this->loadTokenId();
    }

    // 请求结束事件
    public function onRequestEnd()
    {
        parent::onRequestEnd();
        // 关闭连接
        $this->saveHandler->disconnect();
    }

    // 载入TokenID
    public function loadTokenId()
    {
        $this->_tokenId = \Mix::app()->request->get($this->name) or
        $this->_tokenId = \Mix::app()->request->header($this->name) or
        $this->_tokenId = \Mix::app()->request->post($this->name);
        $this->_tokenKey = $this->_tokenPrefix . $this->_tokenId;
    }

    // 创建TokenID
    public function createTokenId()
    {
        $this->_tokenId  = StringHelper::getRandomString(32);
        $this->_tokenKey = $this->_tokenPrefix . $this->_tokenId;
    }

    // 设置唯一索引
    public function setUniqueIndex($uniqueId)
    {
        $uniqueKey = $this->_uniqueIndexPrefix . $uniqueId;
        // 删除旧token数据
        $beforeTokenId = $this->saveHandler->get($uniqueKey);
        if (!empty($beforeTokenId)) {
            $beforeTokenkey = $this->_tokenPrefix . $beforeTokenId;
            $this->saveHandler->del($beforeTokenkey);
        }
        // 更新唯一索引
        $this->saveHandler->setex($uniqueKey, $this->expires, $this->_tokenId);
        // 在数据中加入索引信息
        $this->saveHandler->hmset($this->_tokenKey, ['__uidx__' => $uniqueId]);
    }

    // 赋值
    public function set($name, $value)
    {
        $success = $this->saveHandler->hmset($this->_tokenKey, [$name => serialize($value)]);
        $this->saveHandler->expire($this->_tokenKey, $this->expires);
        return $success ? true : false;
    }

    // 取值
    public function get($name = null)
    {
        if (is_null($name)) {
            $result = $this->saveHandler->hgetall($this->_tokenKey);
            unset($result['__uidx__']);
            foreach ($result as $key => $item) {
                $result[$key] = unserialize($item);
            }
            return $result ?: [];
        }
        $value = $this->saveHandler->hget($this->_tokenKey, $name);
        return $value === false ? null : unserialize($value);
    }

    // 判断是否存在
    public function has($name)
    {
        $exist = $this->saveHandler->hexists($this->_tokenKey, $name);
        return $exist ? true : false;
    }

    // 删除
    public function delete($name)
    {
        $success = $this->saveHandler->hdel($this->_tokenKey, $name);
        return $success ? true : false;
    }

    // 清除token
    public function clear()
    {
        $success = $this->saveHandler->del($this->_tokenKey);
        return $success ? true : false;
    }

    // 获取TokenId
    public function getTokenId()
    {
        return $this->_tokenId;
    }

    // 刷新token
    public function refresh()
    {
        // 判断 token 是否存在
        $tokenData = $this->saveHandler->hgetall($this->_tokenKey);
        if (empty($tokenData)) {
            return false;
        }
        // 定义变量
        $oldData     = $tokenData;
        $oldTokenKey = $this->_tokenKey;
        $newTokenId  = StringHelper::getRandomString(32);
        $newTokenKey = $this->_tokenPrefix . $newTokenId;
        $uniqueKey   = $this->_uniqueIndexPrefix . $oldData['__uidx__'];
        // 生成新的 token 数据
        $this->saveHandler->del($oldTokenKey);
        $this->saveHandler->hmset($newTokenKey, $oldData);
        $this->saveHandler->set($uniqueKey, $newTokenId);
        $this->saveHandler->expire($newTokenKey, $this->expires);
        $this->saveHandler->expire($uniqueKey, $this->expires);
        // 新 token 赋值到属性
        $this->_tokenId  = $newTokenId;
        $this->_tokenKey = $newTokenKey;
        // 返回
        return true;
    }

}
