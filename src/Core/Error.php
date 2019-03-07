<?php

namespace Mix\Core;

/**
 * Error类
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class Error
{

    /**
     * 已经注册
     * @var bool
     */
    protected static $registered = false;

    /**
     * 注册错误处理
     */
    public static function register()
    {
        // 设置错误级别
        $level = \Mix::$app->error->level;
        if (error_reporting() !== $level) {
            error_reporting($level);
        }
        // 多次注册处理
        if (self::$registered) {
            return;
        }
        self::$registered = true;
        // 注册错误处理
        set_error_handler([__CLASS__, 'appError']);
        set_exception_handler([__CLASS__, 'appException']); // swoole 不支持该函数
        register_shutdown_function([__CLASS__, 'appShutdown']);
    }

    /**
     * 错误处理
     * @param $errno
     * @param $errstr
     * @param string $errfile
     * @param int $errline
     */
    public static function appError($errno, $errstr, $errfile = '', $errline = 0)
    {
        if (error_reporting() & $errno) {
            // 委托给异常处理
            if (self::isFatalWarning($errno, $errstr)) {
                self::appException(new \Mix\Exception\ErrorException($errno, $errstr, $errfile, $errline));
                return;
            }
            // 转换为异常抛出
            throw new \Mix\Exception\ErrorException($errno, $errstr, $errfile, $errline);
        }
    }

    /**
     * 停止处理
     */
    public static function appShutdown()
    {
        if (!is_null($error = error_get_last()) && self::isFatal($error['type'])) {
            // 委托给异常处理
            self::appException(new \Mix\Exception\ErrorException($error['type'], $error['message'], $error['file'], $error['line']));
        }
    }

    /**
     * 异常处理
     * @param $e
     */
    public static function appException($e)
    {
        \Mix::$app->error->handleException($e);
    }

    /**
     * 返回错误类型
     * @param $errno
     * @return string
     */
    public static function getType($errno)
    {
        if (self::isError($errno)) {
            return 'error';
        }
        if (self::isWarning($errno)) {
            return 'warning';
        }
        if (self::isNotice($errno)) {
            return 'notice';
        }
        return 'error';
    }

    /**
     * 是否错误类型
     * 全部类型：http://php.net/manual/zh/errorfunc.constants.php
     * @param $type
     * @return bool
     */
    public static function isError($errno)
    {
        return in_array($errno, [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR]);
    }

    /**
     * 是否警告类型
     * 全部类型：http://php.net/manual/zh/errorfunc.constants.php
     * @param $type
     * @return bool
     */
    public static function isWarning($errno)
    {
        return in_array($errno, [E_WARNING, E_CORE_WARNING, E_COMPILE_WARNING, E_USER_WARNING]);
    }

    /**
     * 是否通知类型
     * 全部类型：http://php.net/manual/zh/errorfunc.constants.php
     * @param $type
     * @return bool
     */
    public static function isNotice($errno)
    {
        return in_array($errno, [E_NOTICE, E_USER_NOTICE, E_DEPRECATED, E_USER_DEPRECATED, E_STRICT]);
    }

    /**
     * 是否为致命错误
     * @param $errno
     * @return bool
     */
    public static function isFatal($errno)
    {
        return in_array($errno, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
    }

    /**
     * 是否致命警告类型
     * 特殊的警告，出现后 try/catch 将无法捕获异常。
     * @param $errno
     * @param $errstr
     * @return bool
     */
    public static function isFatalWarning($errno, $errstr)
    {
        if ($errno == E_WARNING && strpos($errstr, 'require') === 0) {
            return true;
        }
        return false;
    }

}
