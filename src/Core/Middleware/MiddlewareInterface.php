<?php

namespace Mix\Core\Middleware;

/**
 * Interface MiddlewareInterface
 * @package Mix\Core\Middleware
 * @author LIUJIAN <coder.keda@gmail.com>
 */
interface MiddlewareInterface
{

    /**
     * 处理
     * @param callable $callback
     * @param \Closure $next
     * @return mixed
     */
    public function handle(callable $callback, \Closure $next);

}
