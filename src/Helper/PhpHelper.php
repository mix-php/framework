<?php

namespace Mix\Helper;

/**
 * PhpHelper类
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class PhpHelper
{

    // 是否为 CLI 模式
    public static function isCli()
    {
        return PHP_SAPI === 'cli';
    }

    // 是否为 Win 系统
    public static function isWin()
    {
        return stripos(PHP_OS, 'WIN') !== false;
    }

    // 是否为 Mac 系统
    public static function isMac()
    {
        return stripos(PHP_OS, 'Darwin') !== false;
    }

}
