<?php

namespace Mix\Console;

use Mix\Core\BaseObject;
use Mix\Helpers\ProcessHelper;

/**
 * Class PidFileHandler
 * @package Mix\Console
 */
class PidFileHandler extends BaseObject
{

    /**
     * PID文件
     * @var string
     */
    public $pidFile = '';

    /**
     * 写入
     * @return bool
     */
    public function write()
    {
        return file_put_contents($this->pidFile, ProcessHelper::getPid(), LOCK_EX) ? true : false;
    }

    /**
     * 读取
     * @return bool|string
     */
    public function read()
    {
        if (!file_exists($this->pidFile)) {
            return false;
        }
        $pid = file_get_contents($this->pidFile);
        if (!is_numeric($pid) || !ProcessHelper::kill($pid, 0)) {
            return false;
        }
        return $pid;
    }

}
