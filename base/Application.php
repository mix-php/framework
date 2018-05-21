<?php

namespace mix\base;

/**
 * App类
 * @author 刘健 <coder.liu@qq.com>
 *
 * @property \mix\base\Log $log
 * @property \mix\console\Input $input
 * @property \mix\console\Output $output
 * @property \mix\web\Route $route
 * @property \mix\web\Request|\mix\swoole\Request $request
 * @property \mix\web\Response|\mix\swoole\Response $response
 * @property \mix\web\Error|\mix\console\Error $error
 * @property \mix\web\Token $token
 * @property \mix\web\Session $session
 * @property \mix\web\Cookie $cookie
 * @property \mix\client\PDO $rdb
 * @property \mix\client\Redis $redis
 * @property \mix\websocket\TokenReader $tokenReader
 * @property \mix\websocket\SessionReader $sessionReader
 * @property \mix\websocket\MessageHandler $messageHandler
 */
class Application
{

    // 基础路径
    public $basePath = '';

    // 组件配置
    public $components = [];

    // 对象配置
    public $objects = [];

    // 组件容器
    protected $_components;

    // 组件命名空间
    protected $_componentNamespace;

    // 构造
    public function __construct($attributes)
    {
        // 导入属性
        foreach ($attributes as $name => $attribute) {
            $this->$name = $attribute;
        }
        // 快捷引用
        \Mix::setApp($this);
    }

    // 设置组件命名空间
    public function setComponentNamespace($namespace)
    {
        $this->_componentNamespace = $namespace;
    }

    // 创建对象
    public function createObject($name)
    {
        return \Mix::createObject($this->objects[$name]);
    }

    // 装载组件
    public function loadComponent($name)
    {
        // 未注册
        if (!isset($this->components[$name])) {
            throw new \mix\exceptions\ComponentException("组件不存在：{$name}");
        }
        // 使用配置创建新对象
        $object = \Mix::createObject($this->components[$name]);
        // 组件效验
        if (!($object instanceof Component)) {
            throw new \mix\exceptions\ComponentException("不是组件类型：{$this->components[$name]['class']}");
        }
        // 装入容器
        $this->_components[$name] = $object;
    }

    // 获取配置目录路径
    public function getConfigPath()
    {
        return $this->basePath . 'config' . DIRECTORY_SEPARATOR;
    }

    // 获取运行目录路径
    public function getRuntimePath()
    {
        return $this->basePath . 'runtime' . DIRECTORY_SEPARATOR;
    }

}
