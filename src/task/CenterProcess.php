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
        if ($this->type == \mix\task\TaskExecutor::TYPE_CRONTAB) {
            $finished = $this->_table->get('leftFinishStatus', 'value') == 1;
        } else {
            $finished = !ProcessHelper::isRunning($this->mpid);
        }
        if ($finished && $this->queueIsEmpty()) {
            if ($this->type == \mix\task\TaskExecutor::TYPE_CRONTAB) {
                if ($this->mode == \mix\task\TaskExecutor::MODE_PUSH) {
                    // 杀死主进程
                    ProcessHelper::kill($this->mpid);
                } else {
                    // 标记完成
                    $this->table->set('centerFinishStatus', ['value' => 1]);
                }
            }
            // 退出
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
