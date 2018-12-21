<?php

namespace Mix\Http;

use Mix\Core\Component;
use Mix\Core\ComponentInterface;
use Mix\Http\View;

/**
 * Error类
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class Error extends Component
{

    /**
     * 协程模式
     * @var int
     */
    public static $coroutineMode = ComponentInterface::COROUTINE_MODE_REFERENCE;

    /**
     * 格式值
     */
    const FORMAT_HTML = 'html';
    const FORMAT_JSON = 'json';
    const FORMAT_XML = 'xml';

    /**
     * 输出格式
     * @var string
     */
    public $format = self::FORMAT_HTML;

    /**
     * 错误级别
     * 只在 Apache/PHP-FPM 传统环境下有效
     * @var int
     */
    public $level = E_ALL;

    /**
     * 异常处理
     * @param $e
     */
    public function handleException($e)
    {
        // debug处理 & exit处理
        if ($e instanceof \Mix\Exceptions\DebugException || $e instanceof \Mix\Exceptions\EndException) {
            \Mix::$app->response->content = $e->getMessage();
            \Mix::$app->response->send();
            return;
        }
        // 错误参数定义
        $statusCode = $e instanceof \Mix\Exceptions\NotFoundException ? 404 : 500;
        $errors     = [
            'status'  => $statusCode,
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
        // 发送客户端
        self::send($errors);
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
        $message .= '$_SERVER' . substr(print_r(\Mix::$app->request->server() + \Mix::$app->request->header(), true), 5);
        $message .= '$_GET' . substr(print_r(\Mix::$app->request->get(), true), 5);
        $message .= '$_POST' . substr(print_r(\Mix::$app->request->post(), true), 5, -1);
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
     * 发送客户端
     * @param $errors
     */
    protected static function send($errors)
    {
        $statusCode = $errors['status'];
        // 清空系统错误
        ob_get_contents() and ob_clean();
        // 错误响应
        if (!\Mix::$app->appDebug) {
            if ($statusCode == 404) {
                $errors = [
                    'status'  => 404,
                    'message' => $e->getMessage(),
                ];
            }
            if ($statusCode == 500) {
                $errors = [
                    'status'  => 500,
                    'message' => '服务器内部错误',
                ];
            }
        }
        $format                          = \Mix::$app->error->format;
        $tpl                             = [
            404 => "errors.{$format}.not_found",
            500 => "errors.{$format}.internal_server_error",
        ];
        $content                         = (new View())->render($tpl[$statusCode], $errors);
        \Mix::$app->response->statusCode = $statusCode;
        \Mix::$app->response->content    = $content;
        switch ($format) {
            case self::FORMAT_HTML:
                \Mix::$app->response->format = \Mix\Http\Response::FORMAT_HTML;
                break;
            case self::FORMAT_JSON:
                \Mix::$app->response->format = \Mix\Http\Response::FORMAT_JSON;
                break;
            case self::FORMAT_XML:
                \Mix::$app->response->format = \Mix\Http\Response::FORMAT_XML;
                break;
        }
        \Mix::$app->response->send();
    }

}
