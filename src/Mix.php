<?php

/**
 * Mix类
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class Mix
{

    /**
     * 版本号
     */
    const VERSION = '1.1.1';

    /**
     * App实例
     * @var \Mix\Http\Application|\Mix\Console\Application
     */
    public static $app;

    /**
     * 构建配置
     * @param $config
     * @param bool $newInstance
     * @return mixed
     */
    public static function configure($config, $newInstance = false)
    {
        foreach ($config as $key => $value) {
            // 子类实例化
            if (is_array($value)) {
                // 实例化
                if (isset($value['class'])) {
                    $config[$key] = self::configure($value, true);
                }
                // 引用其他组件
                if (isset($value['component'])) {
                    $name         = $value['component'];
                    $config[$key] = self::$app->$name;
                }
            }
        }
        if ($newInstance) {
            $class = $config['class'];
            unset($config['class']);
            return new $class($config);
        }
        return $config;
    }

    /**
     * 导入属性
     * @param $object
     * @param $config
     * @return mixed
     */
    public static function importProperties($object, $config)
    {
        foreach ($config as $name => $value) {
            // 类型检测
            $class      = get_class($object);
            $docComment = (new \ReflectionClass($class))->getProperty($name)->getDocComment();
            if ($docComment) {
                $key   = '@var';
                $len   = strlen($key);
                $start = strpos($docComment, $key);
                if ($start !== false) {
                    $end = strpos($docComment, '*', $start + $len);
                    $tmp = substr($docComment, $start + $len, $end - $start - $len);
                    $tmp = explode(' ', trim($tmp));
                    $var = array_shift($tmp);
                    $var = substr($var, 0, 1) === '\\' ? substr($var, 1) : '';
                    if ($var) {
                        if (!interface_exists($var) && !class_exists($var)) {
                            throw new \Mix\Exceptions\DependencyInjectionException("Interface or class not found, class: {$class}, property: {$name}, @var: {$var}");
                        }
                        if (!($value instanceof $var)) {
                            throw new \Mix\Exceptions\DependencyInjectionException("The type of the imported property does not match, class: {$class}, property: {$name}, @var: {$var}");
                        }
                    }
                }
            }
            // 导入
            $object->$name = $value;
        }
        return $object;
    }

    /**
     * 使用配置创建对象
     * @param $config
     * @return mixed
     */
    public static function createObject($config)
    {
        $class = $config['class'];
        unset($config['class']);
        return new $class($config);
    }

}
