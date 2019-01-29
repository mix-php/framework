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
    public static $version;

    /**
     * App实例
     * @var \Mix\Http\Application|\Mix\Console\Application
     */
    public static $app;

    /**
     * 环境配置
     * @var \Mix\Config\Environment
     */
    public static $env;

    /**
     * 构建配置
     * @param $parent
     * @param $config
     * @param bool $ref
     * @return mixed
     */
    public static function configure($parent, $config, $ref = false)
    {
        foreach ($config as $key => $value) {
            // 子类处理
            if (is_array($value)) {
                // 引用依赖
                if (isset($value['ref'])) {
                    $config[$key] = self::configure($parent, $value, true);
                }
                // 引用组件
                if (isset($value['component'])) {
                    $name         = $value['component'];
                    $config[$key] = self::$app->$name;
                }
            }
        }
        if ($ref) {
            // 实例化
            if (isset($config['ref'])) {
                $name       = $config['ref'];
                $bean       = \Mix\Core\Bean::config($name);
                $class      = $bean['class'];
                $properties = $bean['properties'] ?? [];
                if (!isset($properties['parent'])) {
                    $properties['parent'] = $parent;
                }
                return new $class($properties);
            }
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
            // 注释类型检测
            $class      = get_class($object);
            $docComment = (new \ReflectionClass($class))->getProperty($name)->getDocComment();
            $var        = self::getVarFrom($docComment);
            if ($var) {
                if (!interface_exists($var) && !class_exists($var)) {
                    throw new \Mix\Exceptions\DependencyInjectionException("Interface or class not found, class: {$class}, property: {$name}, @var: {$var}");
                }
                if (!($value instanceof $var)) {
                    throw new \Mix\Exceptions\DependencyInjectionException("The type of the imported property does not match, class: {$class}, property: {$name}, @var: {$var}");
                }
            }
            // 导入
            $object->$name = $value;
        }
        return $object;
    }

    /**
     * 获取注释中var的值
     * @param $docComment
     * @return string
     */
    protected static function getVarFrom($docComment)
    {
        $var = '';
        if (!$docComment) {
            return $var;
        }
        $key   = '@var';
        $len   = 4;
        $start = strpos($docComment, $key);
        $end   = strpos($docComment, '*', $start + $len);
        if ($start !== false && $end !== false) {
            $tmp = substr($docComment, $start + $len, $end - $start - $len);
            $tmp = explode(' ', trim($tmp));
            $var = array_shift($tmp);
            $var = substr($var, 0, 1) === '\\' ? substr($var, 1) : '';
        }
        return $var;
    }

    /**
     * 创建组件
     * @param $config
     * @return mixed
     */
    public static function createComponent($config)
    {
        $name       = $config['ref'];
        $bean       = \Mix\Core\Bean::config($name);
        $class      = $bean['class'];
        $properties = $bean['properties'] ?? [];
        return new $class($properties);
    }

    /**
     * 从文件载入环境配置
     * @param $filename
     * @return bool
     */
    public static function loadEnvironmentFrom($filename)
    {
        $env = new \Mix\Config\Environment(['filename' => $filename]);
        $env->load();
        self::$env = $env;
        return true;
    }

}
