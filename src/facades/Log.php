<?php

namespace mix\facades;

use mix\base\Facade;

/**
 * Log 门面类
 * @author 刘健 <coder.liu@qq.com>
 *
 * @method debug($message) static
 * @method info($message) static
 * @method error($message) static
 * @method write($filePrefix, $message) static
 * @method writeln($filePrefix, $message) static
 */
class Log extends Facade
{

    // 获取实例
    public static function getInstance()
    {
        return \Mix::app()->log;
    }

}
