<?php

namespace Mix\Container;

use Mix\Core\Bean;
use Mix\Core\Component;
use Mix\Core\Coroutine;
use Mix\Core\DIObject;
use Psr\Container\ContainerInterface;

/**
 * 容器类
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class Container extends DIObject implements ContainerInterface
{

    /**
     * 组件配置
     * @var array
     */
    public $config = [];

    /**
     * 容器集合
     * @var array
     */
    protected $_containers = [];

    /**
     * 获取容器
     * @param $name
     * @return ComponentInterface
     */
    public function get($name)
    {
        $tid = $this->getTid($name);
        if (!isset($this->_containers[$tid])) {
            $this->_containers[$tid] = new Bucket([
                'container' => $this,
            ]);
        }
        return $this->_containers[$tid]->get($name);
    }

    /**
     * 判断容器是否存在
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        $tid = $this->getTid($name);
        if (!isset($this->_containers[$tid])) {
            return false;
        }
        return $this->_containers[$tid]->has($name);
    }

    /**
     * 移除容器
     * @param $tid 顶部协程id
     */
    public function delete($tid)
    {
        $this->_containers[$tid] = null;
        unset($this->_containers[$tid]);
    }

    /**
     * 获取顶部协程id
     * @param $name
     * @return int
     */
    protected function getTid($name)
    {
        $tid  = Coroutine::tid();
        $mode = $this->getCoroutineMode($name);
        if ($mode == Component::COROUTINE_MODE_REFERENCE) {
            $tid = -1;
        }
        return $tid;
    }

    /**
     * 获取协程模式
     * @param $name
     * @return mixed
     */
    protected function getCoroutineMode($name)
    {
        // 未注册
        if (!isset($this->config[$name])) {
            throw new \Mix\Exceptions\ComponentException("Did not register this component: {$name}");
        }
        // 提取协程模式
        $bean  = Bean::config($this->config[$name]['ref']);
        $class = $bean['class'];
        // 组件效验
        if (!isset($class::$coroutineMode)) {
            throw new \Mix\Exceptions\ComponentException("This class is not a component: {$class}");
        }
        // 返回
        return $class::$coroutineMode;
    }

}
