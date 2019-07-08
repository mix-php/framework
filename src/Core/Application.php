<?php

namespace Mix\Core;

use Mix\Bean\ApplicationContext;
use Mix\Bean\BeanInjector;
use Mix\Helper\FileSystemHelper;
use Psr\Container\ContainerInterface;

/**
 * Class Application
 * @package Mix\Core
 * @author liu,jian <coder.keda@gmail.com>
 */
class Application implements ContainerInterface
{

    /**
     * 应用名称
     * @var string
     */
    public $appName = 'app-console';

    /**
     * 应用版本
     * @var string
     */
    public $appVersion = '0.0.0';

    /**
     * 应用调试
     * @var bool
     */
    public $appDebug = true;

    /**
     * 基础路径
     * @var string
     */
    public $basePath = '';

    /**
     * 配置路径
     * @var string
     */
    public $configPath = 'config';

    /**
     * 运行目录路径
     * @var string
     */
    public $runtimePath = 'runtime';

    /**
     * 公开目录路径
     * @var string
     */
    public $publicPath = 'public';

    /**
     * 视图目录路径
     * @var string
     */
    public $viewPath = 'views';

    /**
     * 依赖配置
     * @var array
     */
    public $beans = [];

    /**
     * Context
     * @var ApplicationContext
     */
    public $context;

    /**
     * Application constructor.
     */
    public function __construct(array $config)
    {
        // 注入
        BeanInjector::inject($this, $config);
        // 实例化ApplicationContext
        $this->context = new ApplicationContext($this->beans);
    }

    /**
     * 获取Bean
     * @param string $name
     * @return object
     */
    public function get($name)
    {
        return $this->context->getBean($name);
    }

    /**
     * 判断Bean是否存在
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        $beanDefinition = null;
        try {
            $beanDefinition = $this->context->getBeanDefinition($name);
        } catch (\Throwable $e) {
        }
        return $beanDefinition ? true : false;
    }

    /**
     * 获取配置
     * @param $name
     * @return mixed
     */
    public function getConfig($name)
    {
        $message   = "Config does not exist: {$name}.";
        $fragments = explode('.', $name);
        // 判断一级配置是否存在
        $first = array_shift($fragments);
        if (!isset($this->$first)) {
            throw new \Mix\Exception\ConfigException($message);
        }
        // 判断其他配置是否存在
        $current = $this->$first;
        foreach ($fragments as $key) {
            if (!isset($current[$key])) {
                throw new \Mix\Exception\ConfigException($message);
            }
            $current = $current[$key];
        }
        return $current;
    }

    /**
     * 获取配置目录路径
     * @return string
     */
    public function getConfigPath()
    {
        if (!FileSystemHelper::isAbsolute($this->configPath)) {
            if ($this->configPath == '') {
                return $this->basePath;
            }
            return $this->basePath . DIRECTORY_SEPARATOR . $this->configPath;
        }
        return $this->configPath;
    }

    /**
     * 获取运行目录路径
     * @return string
     */
    public function getRuntimePath()
    {
        if (!FileSystemHelper::isAbsolute($this->runtimePath)) {
            if ($this->runtimePath == '') {
                return $this->basePath;
            }
            return $this->basePath . DIRECTORY_SEPARATOR . $this->runtimePath;
        }
        return $this->runtimePath;
    }

    /**
     * 获取视图目录路径
     * @return string
     */
    public function getViewPath()
    {
        if (!FileSystemHelper::isAbsolute($this->viewPath)) {
            if ($this->viewPath == '') {
                return $this->basePath;
            }
            return $this->basePath . DIRECTORY_SEPARATOR . $this->viewPath;
        }
        return $this->viewPath;
    }

    /**
     * 获取公开目录路径
     * @return string
     */
    public function getPublicPath()
    {
        if (!FileSystemHelper::isAbsolute($this->publicPath)) {
            if ($this->publicPath == '') {
                return $this->basePath;
            }
            return $this->basePath . DIRECTORY_SEPARATOR . $this->publicPath;
        }
        return $this->publicPath;
    }

}
