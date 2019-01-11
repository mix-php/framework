<?php

namespace Mix\Core;

/**
 * Trait StaticInstanceTrait
 * @package Mix\Core
 * @author LIUJIAN <coder.keda@gmail.com>
 */
trait StaticInstanceTrait
{

    /**
     * 使用静态方法创建实例
     * @param mixed ...$args
     * @return $this
     */
    public static function new(...$args)
    {
        return new static(...$args);
    }

    /**
     * 使用依赖创建实例
     * @param $name
     * @return $this
     */
    public static function newInstance($name = null)
    {
        $currentClass = get_called_class();
        $bean         = Bean::config(is_null($name) ? Bean::name($currentClass) : $name);
        $class        = $bean['class'];
        $properties   = $bean['properties'] ?? [];
        if ($class != $currentClass) {
            throw new \Mix\Exceptions\ConfigException("Bean class is not equal to the current class, Current class: {$currentClass}, Bean class: {$class}");
        }
        return $object = new $class($properties);
    }

}
