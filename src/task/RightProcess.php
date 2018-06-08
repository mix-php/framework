<?php

namespace mix\task;

use mix\helpers\ProcessHelper;

/**
 * 任务进程类（右）
 * @author 刘健 <coder.liu@qq.com>
 */
class RightProcess extends BaseProcess
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

    // 回退数据
    public function rollback($data, $serialize = true)
    {
        $serialize and $data = serialize($data);
        if (!$this->current->push($data)) {
            throw new \mix\exceptions\TaskException('RightProcess Error: fallback faild.');
        }
        return true;
    }

}
