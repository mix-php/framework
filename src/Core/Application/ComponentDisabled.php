<?php

namespace Mix\Core\Application;

/**
 * Class ComponentDisabled
 * @package Mix\Core\Application
 * @author liu,jian <coder.keda@gmail.com>
 */
class ComponentDisabled
{

    /**
     * @var \Mix\Core\Component\ComponentInterface
     */
    public $_component;

    /**
     * @var string
     */
    public $_name;

    /**
     * ComponentBeforeInitialize constructor.
     * @param $component
     * @param $name
     */
    public function __construct($component, $name)
    {
        $this->_component = $component;
        $this->_name      = $name;
    }

    /**
     * 执行前置初始化
     * @return mixed
     */
    public function beforeInitialize()
    {
        $arguments = func_get_args();
        return call_user_func_array([$this->_component, 'beforeInitialize'], $arguments);
    }

    /**
     * 未初始化错误处理
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        throw new \Mix\Exception\ComponentException("'{$this->_name}' component is no initialize, cannot be used. ");
    }

}
