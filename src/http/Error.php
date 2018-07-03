<?php

namespace mix\http;

use mix\base\Component;
use mix\http\View;

/**
 * Error类
 * @author 刘健 <coder.liu@qq.com>
 */
class Error extends Component
{

    // 格式值
    const FORMAT_HTML = 'html';
    const FORMAT_JSON = 'json';
    const FORMAT_XML = 'xml';

    // 输出格式
    public $format = self::FORMAT_HTML;

    // 错误级别，只在 Apache/PHP-FPM 传统环境下有效
    public $level = E_ALL;

    // 异常处理
    public function handleException(\Exception $e)
    {
        // debug处理 & exit处理
        if ($e instanceof \mix\exceptions\DebugException || $e instanceof \mix\exceptions\EndException) {
            \Mix::app()->response->content = $e->getMessage();
            \Mix::app()->response->send();
            return;
        }
        // 错误参数定义
        $statusCode = $e instanceof \mix\exceptions\NotFoundException ? 404 : 500;
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
        if (isset(\Mix::app()->components['log']) && !($e instanceof \mix\exceptions\NotFoundException)) {
            $time    = date('Y-m-d H:i:s');
            $message = "[time] {$time}" . PHP_EOL;
            $message .= "[code] {$errors['code']}" . PHP_EOL;
            $message .= "[message] {$errors['message']}" . PHP_EOL;
            $message .= "[type] {$errors['type']}" . PHP_EOL;
            $message .= "[file] {$errors['file']} line {$errors['line']}" . PHP_EOL;
            $message .= "[trace] {$errors['trace']}" . PHP_EOL;
            $message .= '$_SERVER' . substr(print_r(\Mix::app()->request->server() + \Mix::app()->request->header(), true), 5);
            $message .= '$_GET' . substr(print_r(\Mix::app()->request->get(), true), 5);
            $message .= '$_POST' . substr(print_r(\Mix::app()->request->post(), true), 5);
            \Mix::app()->log->error($message);
        }
        // 清空系统错误
        ob_get_contents() and ob_clean();
        // 错误响应
        if (!\mix\base\Env::get('APP_DEBUG')) {
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
        $format                           = \Mix::app()->error->format;
        $tpl                              = [
            404 => "errors.{$format}.not_found",
            500 => "errors.{$format}.internal_server_error",
        ];
        $content                          = (new View())->render($tpl[$statusCode], $errors);
        \Mix::app()->response->statusCode = $statusCode;
        \Mix::app()->response->content    = $content;
        switch ($format) {
            case self::FORMAT_HTML:
                \Mix::app()->response->format = \mix\http\Response::FORMAT_HTML;
                break;
            case self::FORMAT_JSON:
                \Mix::app()->response->format = \mix\http\Response::FORMAT_JSON;
                break;
            case self::FORMAT_XML:
                \Mix::app()->response->format = \mix\http\Response::FORMAT_XML;
                break;
        }
        \Mix::app()->response->send();
    }

}
