<?php

namespace mix\task;

use mix\helpers\ProcessHelper;

/**
 * 任务进程类（中）
 * @author 刘健 <coder.liu@qq.com>
 */
class CenterProcess extends BaseProcess
{

    // 定时任务执行状态
    const CRONTAB_STATUS_FINISH = 3;

    // 从队列中提取数据
    public function pop($unserialize = true)
    {
        if ($this->type == \mix\task\TaskExecutor::TYPE_DAEMON) {
            $finished = !ProcessHelper::isRunning($this->mpid);
            if ($finished && $this->table->get('daemonImmediatelyExit', 'value')) {
                $this->current->exit();
            }
        } else {
            $finished = true;
        }
        if ($finished && $this->queueIsEmpty()) {
            if ($this->type == \mix\task\TaskExecutor::TYPE_DAEMON) {
                $this->current->freeQueue();
                $this->current->exit();
            }
            if ($this->type == \mix\task\TaskExecutor::TYPE_CRONTAB) {
                if ($this->table->get('crontabStatus', 'value') == LeftProcess::CRONTAB_STATUS_FINISH && $this->table->decr('crontabCenterUnfinished', 'value') === 0) {
                    $this->table->set('crontabStatus', ['value' => self::CRONTAB_STATUS_FINISH]);
                    $this->current->freeQueue();
                    for ($i = 0; $i < $this->table->get('crontabRightUnfinished', 'value'); $i++) {
                        $this->push(false);
                    }
                    $this->current->exit();
                }
                if ($this->table->get('crontabStatus', 'value') >= self::CRONTAB_STATUS_FINISH) {
                    $this->current->exit();
                }
            }
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
            throw new \mix\exceptions\TaskException("CenterProcess Error: push failed. Data: '{$data}'.");
        }
        return true;
    }

    // 回退数据
    public function rollback($data, $serialize = true)
    {
        $serialize and $data = serialize($data);
        if (!$this->current->push($data)) {
            throw new \mix\exceptions\TaskException("CenterProcess Error: fallback failed. Data: '{$data}'.");
        }
        return true;
    }

}
