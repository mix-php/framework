<?php

namespace mix\task;

use mix\helpers\ProcessHelper;

/**
 * 任务进程类（左）
 * @author 刘健 <coder.liu@qq.com>
 */
class LeftProcess extends BaseProcess
{

    // 投递数据到消息队列中
    public function push($data, $serialize = true)
    {
        $serialize and $data = serialize($data);
        if (!$this->next->push($data)) {
            throw new \mix\exceptions\TaskException('LeftProcess Error: push faild.');
        }
        if (!ProcessHelper::isRunning($this->mpid)) {
            $this->current->exit();
        }
        return true;
    }

    // 结束任务
    public function finish()
    {
        // 杀死主进程
        ProcessHelper::kill($this->mpid);
        while (ProcessHelper::isRunning($this->mpid)) {
            // 等待进程退出
            usleep(100000);
        }
        // 发送一个空数据，解锁阻塞的中进程
        if (isset($this->next) && $this->next->statQueue()['queue_num'] == 0) {
            $this->next->push(serialize(null));
        }
        // 发送一个空数据，解锁阻塞的右进程
        if (isset($this->afterNext) && $this->afterNext->statQueue()['queue_num'] == 0) {
            $this->afterNext->push(serialize(null));
        }
        // 退出
        $this->current->exit();
    }

}
