<?php

namespace Mix\Container;

use Mix\Core\Component;
use Mix\Core\Coroutine;

/**
 * 容器类
 * @author 刘健 <coder.liu@qq.com>
 */
class Container extends BaseObject
{

    /**
     * 组件配置
     * @var array
     */
    protected $config = [];

    /**
     * 容器集合
     * @var array
     */
    protected $containers = [];

    /**
     * 获取容器
     * @param $name
     * @return ComponentInterface
     */
    public function get($name)
    {
        $tid = $this->getTid();
        if (!isset($this->containers[$tid])) {
            $this->containers[$tid] = new \Mix\Container\Container([
                'config' => $this->config,
            ]);
        }
        return $this->containers[$tid]->get($name);
    }

    /**
     * 判断容器是否存在
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        $tid = $this->getTid();
        if (!isset($this->containers[$tid])) {
            return false;
        }
        return $this->containers[$tid]->has($name);
    }

    /**
     * 移除容器
     * @param $tid 顶部协程id
     */
    public function delete($tid)
    {
        $this->containers[$tid] = null;
        unset($this->containers[$tid]);
    }

    /**
     * 获取顶部协程id
     * @return int
     */
    protected function getTid()
    {
        $mode = $this->getCoroutineMode($name);
        $tid  = Coroutine::tid();
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
            throw new \Mix\Exceptions\ComponentException("组件不存在：{$name}");
        }
        // 提取协程模式
        $class = $this->config[$name]['class'];
        // 组件效验
        if (!isset($class::$coroutineMode)) {
            throw new \Mix\Exceptions\ComponentException("不是组件类型：{$class}");
        }
        // 返回
        return $class::$coroutineMode;
    }

}
