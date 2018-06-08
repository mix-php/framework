<?php

namespace mix\task;

use mix\helpers\ProcessHelper;

/**
 * 任务进程类（中）
 * @author 刘健 <coder.liu@qq.com>
 */
class CenterProcess extends BaseProcess
{

    // 从队列中提取数据
    public function pop($unserialize = true)
    {
        if (!ProcessHelper::isRunning($this->mpid) && $this->queueIsEmpty()) {
            $this->current->freeQueue();
            $this->current->exit();
        }
        $data = $this->current->pop();
        if (!empty($data) && $unserialize) {
            $data = unserialize($data);
        }
        return $data;
    }

    // 投递数据到消息队列中
    public function push($data, $serialize = true)
    {
        $serialize and $data = serialize($data);
        if ($this->mode == TaskExecutor::MODE_PUSH) {
            throw new \mix\exceptions\TaskException('CenterProcess Error: method \'push\' is not available in MODE_PUSH mode.');
        }
        if (!$this->next->push($data)) {
            throw new \mix\exceptions\TaskException('CenterProcess Error: push faild.');
        }
        return true;
    }

    // 回退数据
    public function rollback($data, $serialize = true)
    {
        $serialize and $data = serialize($data);
        if (!$this->current->push($data)) {
            throw new \mix\exceptions\TaskException('CenterProcess Error: fallback faild.');
        }
        return true;
    }

}
