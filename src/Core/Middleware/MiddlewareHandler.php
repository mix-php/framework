<?php

namespace Mix\Core\Middleware;

/**
 * Class MiddlewareHandler
 * @package Mix\Core\Middleware
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class MiddlewareHandler
{

    /**
     * 实例集合
     * @var array
     */
    protected $instances = [];

    /**
     * 使用静态方法创建实例
     * @param string $namespace
     * @param array $middleware
     * @return $this
     */
    public static function new(string $namespace, array $middleware)
    {
        return new static($namespace, $middleware);
    }

    /**
     * 构造
     * MiddlewareHandler constructor.
     * @param string $namespace
     * @param array $middleware
     */
    public function __construct(string $namespace, array $middleware)
    {
        $this->instances = static::newInstances($namespace, $middleware);
    }

    /**
     * 执行中间件
     * @param callable $callback
     * @param mixed ...$params
     * @return mixed
     */
    public function run(callable $callback, ...$params)
    {
        $item = array_shift($this->instances);
        if (empty($item)) {
            return call_user_func_array($callback, $params);
        }
        return $item->handle($callback, function () use ($callback, $params) {
            return $this->run($callback, $params);
        });
    }

    /**
     * 实例化中间件
     * @param string $namespace
     * @param array $middleware
     * @return array
     */
    protected static function newInstances(string $namespace, array $middleware)
    {
        $instances = [];
        foreach ($middleware as $key => $name) {
            $class  = "{$namespace}\\{$name}Middleware";
            $object = new $class();
            if (!($object instanceof MiddlewareInterface)) {
                throw new \RuntimeException("{$class} type is not 'Mix\Core\Middleware\MiddlewareInterface'");
            }
            $instances[$key] = $object;
        }
        return $instances;
    }

}
