<?php

namespace mix\console;

use mix\base\BaseObject;

/**
 * 终端类
 * @author 刘健 <coder.liu@qq.com>
 */
class Output extends BaseObject
{

    // 实例
    protected static $instance;

    // 颜色
    const NONE = "\033[0m";
    const BOLD = "\033[1m";

    const FG_BLACK = "\033[30m";
    const FG_RED = "\033[31m";
    const FG_GREEN = "\033[32m";
    const FG_YELLOW = "\033[33m";
    const FG_BLUE = "\033[34m";

    const BG_BLACK = "\033[30m";
    const BG_RED = "\033[41m";
    const BG_GREEN = "\033[42m";
    const BG_YELLOW = "\033[43m";
    const BG_BLUE = "\033[44m";

    // 是否为 WIN 操作系统
    public $isWin;

    // 获取实例
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    // 构造事件
    public function onConstruct()
    {
        $this->isWin = substr(PHP_OS, 0, 3) === 'WIN' ? true : false;
    }

    // 写入
    public function write($message, $color = self::NONE)
    {
        if ($this->isWin) {
            echo $message;
            return;
        }
        echo $color . $message . self::NONE;
    }

    // 写入，带换行
    public function writeln($message, $color = self::NONE)
    {
        if ($this->isWin) {
            echo $message . PHP_EOL;
            return;
        }
        echo $color . $message . self::NONE . PHP_EOL;
    }

}
