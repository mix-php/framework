<?php

namespace mix\task;

use mix\helpers\ProcessHelper;

/**
 * 任务进程类（右）
 * @author 刘健 <coder.liu@qq.com>
 */
class RightProcess extends BaseProcess
{

    // 定时任务执行状态
    const CRONTAB_STATUS_FINISH = 4;

    // 从队列中提取数据
    public function pop($unserialize = true)
    {
        if ($this->type == \mix\task\TaskExecutor::TYPE_CRONTAB) {
            $finished = true;
        } else {
            $finished = !ProcessHelper::isRunning($this->mpid);
        }
        if ($finished && $this->queueIsEmpty()) {
            if ($this->type == \mix\task\TaskExecutor::TYPE_CRONTAB) {
                if ($this->table->get('crontabRunStatus', 'value') == CenterProcess::CRONTAB_STATUS_FINISH && $this->table->decr('crontabRightUnfinished', 'value') === 0) {
                    $this->table->set('crontabRunStatus', ['value' => self::CRONTAB_STATUS_FINISH]);
                    $this->current->freeQueue();
                    $this->current->exit();
                }
                if ($this->table->get('crontabRunStatus', 'value') >= self::CRONTAB_STATUS_FINISH) {
                    $this->current->exit();
                }
            }
            if ($this->type == \mix\task\TaskExecutor::TYPE_DAEMON) {
                $this->current->freeQueue();
                $this->current->exit();
            }
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
