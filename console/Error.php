<?php

namespace mix\console;

use mix\base\Component;

/**
 * Error类
 * @author 刘健 <coder.liu@qq.com>
 */
class Error extends Component
{

    // 注册异常处理
    public static function register()
    {
        error_reporting(E_ALL);
        set_error_handler(['mix\console\Error', 'appError']);
        set_exception_handler(['mix\console\Error', 'appException']);
        register_shutdown_function(['mix\console\Error', 'appShutdown']);
    }

    // Error Handler
    public static function appError($errno, $errstr, $errfile = '', $errline = 0)
    {
        throw new \mix\exceptions\ErrorException($errno, $errstr, $errfile, $errline);
    }

    // Error Handler
    public static function appShutdown()
    {
        if ($error = error_get_last()) {
            self::appException(new \mix\exceptions\ErrorException($error['type'], $error['message'], $error['file'], $error['line']));
        }
    }

    // Exception Handler
    public static function appException($e)
    {
        // debug处理 & exit处理
        if ($e instanceof \mix\exceptions\DebugException || $e instanceof \mix\exceptions\EndException) {
            \Mix::app()->response->content = $e->getMessage();
            \Mix::app()->response->send();
            return;
        }
        // 错误参数定义
        $errors = [
            'code'    => $e->getCode(),
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'type'    => get_class($e),
            'trace'   => $e->getTraceAsString(),
        ];
        // 日志处理
        if (isset(\Mix::app()->components['log']) && !($e instanceof \mix\exceptions\NotFoundException)) {
            $time    = date('Y-m-d H:i:s');
            $message = "[message] {$errors['message']}" . PHP_EOL;
            $message .= "[time] {$time}" . PHP_EOL;
            $message .= "[type] {$errors['type']} code {$errors['code']}" . PHP_EOL;
            $message .= "[file] {$errors['file']} line {$errors['line']}" . PHP_EOL;
            $message .= "[trace] {$errors['trace']}" . PHP_EOL;
            $message .= '$_SERVER' . substr(print_r($_SERVER, true), 5);
            \Mix::app()->log->error($message);
        }
        // 清空系统错误
        ob_get_contents() and ob_clean();
        // 格式化
        $message = $errors['message'] . PHP_EOL;
        $message .= "{$errors['type']} code {$errors['code']}" . PHP_EOL;
        $message .= $errors['file'] . ' line ' . $errors['line'] . PHP_EOL;
        $message .= str_replace("\n", PHP_EOL, $errors['trace']);
        // 输出
        $output = Output::getInstance();
        if (!$output->isWin) {
            // 增加边距
            $message = str_repeat(' ', 4) . str_replace(PHP_EOL, PHP_EOL . str_repeat(' ', 4), $message);
            $message = str_repeat(PHP_EOL, 2) . $message . str_repeat(PHP_EOL, 1);
            $output->writeln($message, Output::BG_RED);
            $output->writeln('');
        } else {
            $output->write($message);
        }
    }

    // 手动处理异常
    public function exception($e)
    {
        self::appException($e);
    }

}
