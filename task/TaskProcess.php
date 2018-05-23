<?php

namespace mix\task;

use mix\process\Process;

/**
 * 任务进程类
 * @author 刘健 <coder.liu@qq.com>
 */
class TaskProcess extends \Swoole\Process
{

    // 进程类型
    const PRODUCER = 0;
    const CONSUMER = 1;

    // 主进程pid
    protected $_masterPid = 0;

    // 设置主进程pid
    public function setMasterPid($pid)
    {
        $this->_masterPid = $pid;
    }

    // 检查主进程
    public function checkMaster($processType = self::CONSUMER)
    {
        if (!Process::isRunning($this->_masterPid)) {
            if ($processType == self::PRODUCER) {
                // 如果队列没有数据，就删除队列，释放堵塞的消费者进程
                if ($this->statQueue()['queue_num'] == 0) {
                    $this->freeQueue();
                }
                $this->exit();
            }
            if ($processType == self::CONSUMER) {
                // 如果队列没有数据，就删除队列，释放其他堵塞的消费者进程
                // 如果队列有数据，继续执行
                if ($this->statQueue()['queue_num'] == 0) {
                    $this->freeQueue();
                    $this->exit();
                }
            }
        }
    }

    // 杀死主进程
    public function killMaster()
    {
        Process::kill($this->_masterPid);
    }

    // 投递数据到消息队列中
    public function push($data)
    {
        return parent::push($data);
    }

    // 从队列中提取数据
    public function pop($maxsize = 8192)
    {
        return parent::pop($maxsize);
    }

}
