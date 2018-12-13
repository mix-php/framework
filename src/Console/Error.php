<?php

namespace Mix\Console;

use Mix\Console\CommandLine\Color;
use Mix\Core\Component;
use Mix\Core\Coroutine;
use Mix\Helpers\ProcessHelper;

/**
 * Error类
 * @author 刘健 <coder.liu@qq.com>
 */
class Error extends Component
{

    /**
     * 错误级别
     * @var int
     */
    public $level = E_ALL;

    /**
     * 异常处理
     * @param $e
     * @param bool $exit
     */
    public function handleException($e, $exit = false)
    {
        // debug处理
        if ($e instanceof \Mix\Exceptions\DebugException) {
            $content = $e->getMessage();
            echo $content;
            $this->exit(0);
        }
        // exit处理
        if ($e instanceof \Mix\Exceptions\EndException) {
            $this->exit($e->getCode());
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
        if (!($e instanceof \Mix\Exceptions\NotFoundException)) {
            self::log($errors);
        }
        // 打印到屏幕
        self::print($errors);
        // 退出
        if ($exit) {
            self::exit(1);
        }
    }

    /**
     * 写入日志
     * @param $errors
     */
    protected static function log($errors)
    {
        // 构造消息
        $message = "{$errors['message']}" . PHP_EOL;
        $message .= "[type] {$errors['type']} [code] {$errors['code']}" . PHP_EOL;
        $message .= "[file] {$errors['file']} [line] {$errors['line']}" . PHP_EOL;
        $message .= "[trace] {$errors['trace']}" . PHP_EOL;
        $message .= '$_SERVER' . substr(print_r($_SERVER, true), 5, -1);
        // 写入
        $errorType = \Mix\Core\Error::getType($errors['code']);
        switch ($errorType) {
            case 'error':
                \Mix::$app->log->error($message);
                break;
            case 'warning':
                \Mix::$app->log->warning($message);
                break;
            case 'notice':
                \Mix::$app->log->notice($message);
                break;
        }
    }

    /**
     * 打印到屏幕
     * @param $errors
     */
    protected static function print($errors)
    {
        // 清空系统错误
        ob_get_contents() and ob_clean();
        // 直接输出
        if ($errors['type'] == 'Mix\Exceptions\NotFoundException' || !\Mix::$app->appDebug) {
            println($errors['message']);
            return;
        }
        // 打印到屏幕，带颜色
        self::printColor($errors);
    }

    /**
     * 打印到屏幕，带颜色
     * @param $errors
     */
    protected static function printColor($errors)
    {
        Color::new(Color::BG_RED)->println($errors['message']);
        Color::new()->println("{$errors['type']} code {$errors['code']}");
        Color::new(Color::BG_RED)->print($errors['file']);
        Color::new()->print(' line ');
        Color::new(Color::BG_RED)->println($errors['line']);
        Color::new()->println(str_replace("\n", PHP_EOL, $errors['trace']));
    }

    /**
     * 退出
     * @param $exitCode
     */
    protected static function exit($code)
    {
        if (Coroutine::id() == -1) {
            exit($code);
        } else {
            ProcessHelper::kill(ProcessHelper::getPid(), SIGKILL);
        }
    }

}
