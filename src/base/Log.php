<?php

namespace mix\base;

/**
 * Log组件
 * @author 刘健 <coder.liu@qq.com>
 */
class Log extends Component
{

    // 轮转规则
    const ROTATE_HOUR = 0;
    const ROTATE_DAY = 1;
    const ROTATE_WEEKLY = 2;

    // 日志目录
    public $logDir = 'log';

    // 日志记录级别
    public $level = ['error', 'info', 'debug'];

    // 日志轮转类型
    public $logRotate = self::ROTATE_DAY;

    // 最大文件尺寸
    public $maxFileSize = 0;

    // 换行符
    public $newline = PHP_EOL;

    // 调试日志
    public function debug($message)
    {
        in_array('debug', $this->level) and $this->write('debug', $message);
    }

    // 信息日志
    public function info($message)
    {
        in_array('info', $this->level) and $this->write('info', $message);
    }

    // 错误日志
    public function error($message)
    {
        in_array('error', $this->level) and $this->write('error', $message);
    }

    // 写入日志
    public function write($filePrefix, $message)
    {
        switch ($this->logRotate) {
            case self::ROTATE_HOUR:
                $timeFormat = date('YmdH');
                break;
            case self::ROTATE_DAY:
                $timeFormat = date('Ymd');
                break;
            case self::ROTATE_WEEKLY:
                $timeFormat = date('YW');
                break;
            default:
                $timeFormat = date('Ymd');
                break;
        }
        $filename = "{$filePrefix}_{$timeFormat}";
        $dir      = $this->logDir;
        if (pathinfo($this->logDir)['dirname'] == '.') {
            $dir = \Mix::app()->getRuntimePath() . $this->logDir;
        }
        is_dir($dir) or mkdir($dir);
        $file   = $dir . '/' . $filename . '.log';
        $number = 0;
        while (file_exists($file) && $this->maxFileSize > 0 && filesize($file) >= $this->maxFileSize) {
            $file = $dir . '/' . $filename . '_' . ++$number . '.log';
        }
        file_put_contents($file, $message . $this->newline, FILE_APPEND | LOCK_EX);
    }

}
