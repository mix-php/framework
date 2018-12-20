<?php

namespace Mix\Core;

/**
 * Trait StaticInstanceTrait
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
     * 创建实例，通过默认配置名
     * @return $this
     */
    public static function newInstance()
    {
        return self::newInstanceByName('default');
    }

    /**
     * 创建实例，通过配置名
     * @param $name
     * @return $this
     */
    public static function newInstanceByName($name)
    {
        // 获取类库配置信息
        $config = self::getLibrariesConfig();
        // 实例化
        $class = get_called_class();
        $key   = "{$class}#{$name}";
        if (!isset($config[$key])) {
            throw new \Mix\Exceptions\ConfigException("Class config does not exist: {$class} name {$name}.");
        }
        return \Mix::createObject($config[$key]);
    }

    /**
     * 获取类库配置信息
     * @return array
     */
    private static function getLibrariesConfig()
    {
        static $cache;
        if (!isset($cache)) {
            $config = \Mix::$app->libraries;
            $data   = [];
            foreach ($config as $item) {
                if (!isset($item['class'])) {
                    throw new \Mix\Exceptions\ConfigException("Libraries config error: class field is not configured.");
                }
                $class = $item['class'];
                $name  = 'default';
                if (is_array($class)) {
                    $class = array_shift($class);
                    if (isset($class['name'])) {
                        $name = $class['name'];
                    }
                }
                $item['class'] = $class;
                $key           = "{$class}#{$name}";
                $data[$key]    = $item;
            }
            $cache = $data;
        }
        return $cache;
    }

}
