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
     * 执行中间件
     * @param callable $callback
     * @param array $params
     * @param array $middlewares
     * @return mixed
     */
    public static function run(callable $callback, array $params, array $middlewares)
    {
        $item = array_shift($middlewares);
        if (empty($item)) {
            return call_user_func_array($callback, $params);
        }
        return $item->handle($callback, function () use ($callback, $params, $middlewares) {
            return self::run($callback, $params, $middlewares);
        });
    }

    /**
     * 实例化中间件
     * @param string $namespace
     * @param array $middlewares
     * @return array
     */
    public static function newInstances(string $namespace, array $middlewares)
    {
        $instances = [];
        foreach ($middlewares as $key => $name) {
            $class  = "{$namespace}\\{$name}Middleware";
            $object = new $class();
            if (!($object instanceof MiddlewareInterface)) {
                throw new \RuntimeException("{$class} type is not 'Mix\Http\Middleware\MiddlewareInterface'");
            }
            $instances[$key] = $object;
        }
        return $instances;
    }

}
