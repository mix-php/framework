<?php

namespace Mix\Helpers;

/**
 * ProcessHelper类
 * @author 刘健 <coder.liu@qq.com>
 */
class ProcessHelper
{

    // 使当前进程蜕变为一个守护进程
    public static function daemon($closeStandardInputOutput = true)
    {
        return \Swoole\Process::daemon(true, !$closeStandardInputOutput);
    }

    // 设置进程标题
    public static function setProcessTitle($title)
    {
        if (PhpInfoHelper::isMac()) {
            return false;
        }
        if (!function_exists('cli_set_process_title')) {
            return false;
        }
        return @cli_set_process_title($title);
    }

    // kill 进程
    public static function kill($pid, $signal = null)
    {
        if (is_null($signal)) {
            return \Swoole\Process::kill($pid);
        }
        return \Swoole\Process::kill($pid, $signal);
    }

    // 返回当前进程ID
    public static function getPid()
    {
        return getmypid();
    }

}
