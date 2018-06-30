<?php

namespace mix\task;

use mix\base\BaseObject;

/**
 * 任务进程类
 * @author 刘健 <coder.liu@qq.com>
 */
class BaseProcess extends BaseObject
{

    // 类型
    public $type;

    // 模式
    public $mode;

    // 进程标志
    public $index;

    // 主进程pid
    public $mpid;

    // 当前进程pid
    public $pid;

    // 任务超时时间 (秒)
    public $timeout;

    // 当前对象
    public $current;

    // 下一步的对象
    public $next;

    // 下下步的对象
    public $afterNext;

    // 共享内存表
    public $table;

    // 队列是否为空，只在主进程关闭时使用
    protected function queueIsEmpty()
    {
        $waitTime     = $this->timeout * 1000000;
        $intervalTime = 100000;
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
