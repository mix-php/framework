<?php

namespace mix\task;

use mix\base\BaseObject;
use mix\process\ProcessHelper;

/**
 * 任务进程类
 * @author 刘健 <coder.liu@qq.com>
 */
class BaseProcess extends BaseObject
{

    // 模式
    public $mode;


    // 进程编号
    public $number;

    // 主进程pid
    public $mpid;

    // 当前进程pid
    public $pid;

    // POP退出等待时间 (秒)
    public $popExitWait;

    // 当前对象
    public $current;

    // 下一步对象
    public $next;

    // 杀死主进程
    public function killMasterProcess()
    {
        ProcessHelper::kill($this->mpid);
    }

    // 队列是否为空，只在主进程关闭时使用
    protected function queueIsEmpty()
    {
        $waitTime     = $this->popExitWait * 1000000;
        $intervalTime = 10000;
        while ($this->current->statQueue()['queue_num'] == 0) {
            if ($waitTime <= 0) {
                return true;
            }
            usleep($intervalTime);
            $waitTime -= $intervalTime;
        }
        return false;
    }

}
