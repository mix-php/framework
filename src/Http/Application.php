<?php

namespace Mix\Http;

use Mix\Core\Component;
use Mix\Core\Coroutine;
use Mix\Container\Container;

/**
 * App类
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class Application extends \Mix\Core\Application
{

    // 控制器命名空间
    public $controllerNamespace = '';

    // 中间件命名空间
    public $middlewareNamespace = '';

    // 全局中间件
    public $middleware = [];

    // 执行功能
    public function run()
    {
        $server                       = \Mix::$app->request->server();
        $method                       = strtoupper($server['request_method']);
        $action                       = empty($server['path_info']) ? '' : substr($server['path_info'], 1);
        \Mix::$app->response->content = $this->runAction($method, $action);
        \Mix::$app->response->send();
    }

    // 执行功能并返回
    public function runAction($method, $action)
    {
        $action = "{$method} {$action}";
        // 路由匹配
        $result = \Mix::$app->route->match($action);
        foreach ($result as $item) {
            list($route, $queryParams) = $item;
            // 路由参数导入请求类
            \Mix::$app->request->setRoute($queryParams);
            // 实例化控制器
            list($shortClass, $shortAction) = $route;
            $controllerDir    = \Mix\Helpers\FileSystemHelper::dirname($shortClass);
            $controllerDir    = $controllerDir == '.' ? '' : "$controllerDir\\";
            $controllerName   = \Mix\Helpers\NameHelper::snakeToCamel(\Mix\Helpers\FileSystemHelper::basename($shortClass), true);
            $controllerClass  = "{$this->controllerNamespace}\\{$controllerDir}{$controllerName}Controller";
            $shortAction      = \Mix\Helpers\NameHelper::snakeToCamel($shortAction, true);
            $controllerAction = "action{$shortAction}";
            // 判断类是否存在
            if (class_exists($controllerClass)) {
                $controllerInstance = new $controllerClass();
                // 判断方法是否存在
                if (method_exists($controllerInstance, $controllerAction)) {
                    // 执行中间件
                    $middleware = $this->newMiddlewareInstance($route['middleware']);
                    if (!empty($middleware)) {
                        return $this->runMiddleware([$controllerInstance, $controllerAction], $middleware);
                    }
                    // 直接返回执行结果
                    return $controllerInstance->$controllerAction();
                }
            }
            // 不带路由参数的路由规则找不到时，直接抛出错误
            if (empty($queryParams)) {
                break;
            }
        }
        throw new \Mix\Exceptions\NotFoundException('Not Found (#404)');
    }

    // 执行中间件
    protected function runMiddleware($callable, $middleware)
    {
        $item = array_shift($middleware);
        if (empty($item)) {
            return call_user_func($callable);
        }
        return $item->handle($callable, function () use ($callable, $middleware) {
            return $this->runMiddleware($callable, $middleware);
        });
    }

    // 实例化中间件
    protected function newMiddlewareInstance($routeMiddleware)
    {
        $middleware = [];
        foreach (array_merge($this->middleware, $routeMiddleware) as $key => $name) {
            $class            = "{$this->middlewareNamespace}\\{$name}Middleware";
            $middleware[$key] = new $class();
        }
        return $middleware;
    }

    // 获取组件
    public function __get($name)
    {
        // 从容器返回组件
        $component = $this->container->get($name);
        // 触发请求前置事件
        self::triggerRequestBefore($component);
        // 返回组件
        return $component;
    }

    // 清扫组件容器
    public function cleanComponents()
    {
        // 触发请求后置事件
        foreach (array_keys($this->components) as $name) {
            $component = $this->container->get($name);
            self::triggerRequestAfter($component);
        }
    }

    // 触发请求前置事件
    protected static function triggerRequestBefore($component)
    {
        if ($component->getStatus() == Component::STATUS_READY) {
            $component->onRequestBefore();
        }
    }

    // 触发请求后置事件
    protected static function triggerRequestAfter($component)
    {
        if ($component->getStatus() == Component::STATUS_RUNNING) {
            $component->onRequestAfter();
        }
    }

    // 获取公开目录路径
    public function getPublicPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'public';
    }

    // 获取视图目录路径
    public function getViewPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'views';
    }

    // 打印变量的相关信息
    public function dump($var, $send = false)
    {
        ob_start();
        var_dump($var);
        $dumpContent                  = ob_get_clean();
        \Mix::$app->response->content .= $dumpContent;
        if ($send) {
            throw new \Mix\Exceptions\DebugException(\Mix::$app->response->content);
        }
    }

    // 终止程序
    public function end($content = '')
    {
        throw new \Mix\Exceptions\EndException($content);
    }

}
