<?php

namespace Mix\Core;

/**
 * Class Coroutine
 * @package Mix\Core
 * @author liu,jian <coder.keda@gmail.com>
 */
class Coroutine
{

    /**
     * id映射
     * @var array
     */
    private static $idMap = [];

    /**
     * tid计数
     * @var array
     */
    private static $tidCount = [];

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
     * 启用协程钩子
     */
    public static function enableHook()
    {
        static $trigger = false;
        if (!$trigger) {
            \Swoole\Runtime::enableCoroutine(true); // Swoole >= 4.1.0
            $trigger = true;
        }
    }

    /**
     * 禁用内置协程
     */
    public static function disableBuiltin()
    {
        // 兼容非 Swoole Console
        if (!function_exists('swoole_async_set')) {
            return;
        }
        // 禁用
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
     * @param callable $function
     * @param mixed ...$params
     */
    public static function create(callable $function, ...$params)
    {
        $tid = self::tid();
        $top = $tid == self::id();
        go(function () use ($function, $params, $tid, $top) {
            // 记录协程id关系
            $id = self::id();
            if ($top && $tid == -1) {
                $tid = $id;
            }
            self::$idMap[$id]     = $tid;
            self::$tidCount[$tid] = self::$tidCount[$tid] ?? 0;
            self::$tidCount[$tid]++;
            // 执行闭包
            try {
                call_user_func_array($function, $params);
            } catch (\Throwable $e) {
                // 输出错误
                \Mix::$app->error->handleException($e);
            }
            // 清理协程资源
            unset(self::$idMap[$id]);
            self::$tidCount[$tid]--;
            // 清除协程
            if (self::$tidCount[$tid] == 0) {
                unset(self::$tidCount[$tid]);
                \Mix::$app->container->delete($tid);
            }
        });
    }

}
