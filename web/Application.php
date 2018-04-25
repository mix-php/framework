<?php

namespace mix\web;

use mix\base\Component;

/**
 * App类
 * @author 刘健 <coder.liu@qq.com>
 */
class Application extends \mix\base\Application
{

    // 控制器命名空间
    public $controllerNamespace = '';

    // 执行功能 (Apache/PHP-FPM)
    public function run()
    {
        \mix\web\Error::register();
        $server                        = \Mix::app()->request->server();
        $method                        = strtoupper($server['request_method']);
        $action                        = empty($server['path_info']) ? '' : substr($server['path_info'], 1);
        \Mix::app()->response->content = $this->runAction($method, $action);
        \Mix::app()->response->send();
        $this->cleanComponents();
    }

    // 执行功能并返回
    public function runAction($method, $action)
    {
        $action = "{$method} {$action}";
        // 路由匹配
        $items = \Mix::app()->route->match($action);
        foreach ($items as $item) {
            list($action, $queryParams) = $item;
            // 执行功能
            if ($action) {
                // 路由参数导入请求类
                \Mix::app()->request->setRoute($queryParams);
                // 实例化控制器
                $controllerAction = "{$this->controllerNamespace}\\{$action}";
                $controllerFull   = \mix\helpers\FilesystemHelper::dirname($controllerAction);
                $controllerPath   = \mix\helpers\FilesystemHelper::dirname($controllerFull);
                $controllerName   = \mix\helpers\FilesystemHelper::snakeToCamel(\mix\helpers\FilesystemHelper::basename($controllerFull), true);
                $controllerMethod = \mix\helpers\FilesystemHelper::snakeToCamel(\mix\helpers\FilesystemHelper::basename($controllerAction), true);
                $controllerClass  = "{$controllerPath}\\{$controllerName}Controller";
                $controllerAction = "action{$controllerMethod}";
                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass();
                    // 判断方法是否存在
                    if (method_exists($controller, $controllerAction)) {
                        // Web 应用
                        if ($this instanceof \mix\web\Application) {
                            // 执行前置动作
                            $controller->beforeAction($controllerAction);
                            // 执行控制器的方法
                            $result = $controller->$controllerAction();
                            // 执行后置动作
                            $controller->afterAction($controllerAction);
                            // 返回执行结果
                            return $result;
                        }
                        // 非 Web 应用
                        return $controller->$controllerAction();
                    }
                }
            }
        }
        throw new \mix\exceptions\NotFoundException('Not Found (#404)');
    }

    // 获取组件
    public function __get($name)
    {
        if (!is_null($this->_componentNamespace)) {
            $name = "{$this->_componentNamespace}.{$name}";
        }
        // 返回单例
        if (isset($this->_components[$name])) {
            // 触发请求开始事件
            if ($this->_components[$name]->getStatus() == Component::STATUS_READY) {
                $this->_components[$name]->onRequestStart();
            }
            // 返回对象
            return $this->_components[$name];
        }
        // 装载组件
        $this->loadComponent($name);
        // 触发请求开始事件
        $this->_components[$name]->onRequestStart();
        // 返回对象
        return $this->_components[$name];
    }

    // 装载全部组件
    public function loadAllComponent()
    {
        foreach (array_keys($this->components) as $name) {
            $this->loadComponent($name);
        }
    }

    // 清扫组件容器
    public function cleanComponents()
    {
        foreach ($this->_components as $component) {
            if ($component->getStatus() == Component::STATUS_RUNNING) {
                $component->onRequestEnd();
            }
        }
    }

    // 获取公开目录路径
    public function getPublicPath()
    {
        return $this->basePath . 'public' . DIRECTORY_SEPARATOR;
    }

    // 获取视图目录路径
    public function getViewPath()
    {
        return $this->basePath . 'views' . DIRECTORY_SEPARATOR;
    }

}
