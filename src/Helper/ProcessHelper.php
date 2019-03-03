<?php

namespace Mix\Helper;

/**
 * ProcessHelper类
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class ProcessHelper
{

    /**
     * 使当前进程蜕变为一个守护进程
     * @param bool $close
     */
    public static function daemon($close = true)
    {
        return \Swoole\Process::daemon(true, !$close);
    }

    /**
     * 设置进程标题
     * @param $title
     * @return bool
     */
    public static function setProcessTitle($title)
    {
        if (PhpHelper::isMac() || PhpHelper::isWin()) {
            return false;
        }
        if (!function_exists('cli_set_process_title')) {
            return false;
        }
        return @cli_set_process_title($title);
    }

    /**
     * kill 进程
     * @param $pid
     * @param null $signal
     * @return bool
     */
    public static function kill($pid, $signal = null)
    {
        if (is_null($signal)) {
            return \Swoole\Process::kill($pid);
        }
        return \Swoole\Process::kill($pid, $signal);
    }

    /**
     * 返回当前进程ID
     * @return int
     */
    public static function getPid()
    {
        return getmypid();
    }

    /**
     * 批量设置异步信号监听
     * @param $signals array
     * @param $callback callable|null
     */
    public static function signal($signals, $callback)
    {
        foreach ($signals as $signal) {
            if (is_null($callback)) {
                \Swoole\Process::signal($signal, null);
                continue;
            }
            \Swoole\Process::signal($signal, function ($signal) use ($callback) {
                try {
                    call_user_func($callback, $signal);
                } catch (\Throwable $e) {
                    \Mix::$app->error->handleException($e);
                }
            });
        }
    }

}
