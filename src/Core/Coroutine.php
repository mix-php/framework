<?php

namespace Mix\Core;

/**
 * Coroutine类
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class Coroutine
{

    /**
     * id映射
     * @var array
     */
    private static $idMap = [];

    /**
     * 获取协程id
     * @return int
     */
    public static function id()
    {
        $id = -1;
        if (!class_exists('\Swoole\Coroutine')) {
            return $id;
        }
        return \Swoole\Coroutine::getuid();
    }

    /**
     * 获取顶部协程id
     * @return int
     */
    public static function tid()
    {
        $id = self::id();
        return self::$idMap[$id] ?? $id;
    }

    /**
     * 开启协程
     */
    public static function enable()
    {
        static $trigger = false;
        if (!$trigger) {
            \Swoole\Runtime::enableCoroutine(true); // Swoole >= 4.1.0
            $trigger = true;
        }
    }

    /**
     * 关闭协程
     */
    public static function disable()
    {
        static $trigger = false;
        if (!$trigger) {
            swoole_async_set([
                'enable_coroutine' => false,
            ]);
            $trigger = true;
        }
    }

    /**
     * 创建协程
     * @param $closure
     */
    public static function create($closure)
    {
        $tid = self::tid();
        $top = $tid == self::id();
        go(function () use ($closure, $tid, $top) {
            // 记录协程id关系
            $id = self::id();
            if ($top && $tid == -1) {
                $tid = $id;
            }
            self::$idMap[$id] = $tid;
            // 执行闭包
            try {
                $closure();
            } catch (\Throwable $e) {
                // 输出错误
                \Mix::$app->error->handleException($e);
            }
            // 清理协程资源
            unset(self::$idMap[$id]);
            // 只在命令行的顶级协程结束时才移除容器
            if ($top && \Mix::$app instanceof \Mix\Console\Application) {
                \Mix::$app->container->delete($tid);
            }
        });
    }

}
