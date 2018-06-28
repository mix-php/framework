<?php

namespace mix\task;

use mix\helpers\ProcessHelper;

/**
 * 任务进程类（左）
 * @author 刘健 <coder.liu@qq.com>
 */
class LeftProcess extends BaseProcess
{

    // 定时任务执行状态
    const CRONTAB_STATUS_START = 1;
    const CRONTAB_STATUS_FINISH = 2;

    // 投递数据到消息队列中
    public function push($data, $serialize = true)
    {
        $serialize and $data = serialize($data);
        if (!$this->next->push($data)) {
            throw new \mix\exceptions\TaskException('LeftProcess Error: push faild, data: ' . $data);
        }
        if ($this->type == \mix\task\TaskExecutor::TYPE_DAEMON && !ProcessHelper::isRunning($this->mpid)) {
            $this->current->exit();
        }
        if ($this->type == \mix\task\TaskExecutor::TYPE_CRONTAB && $this->table->get('crontabStatus', 'value') < self::CRONTAB_STATUS_START) {
            $this->table->set('crontabStatus', ['value' => self::CRONTAB_STATUS_START]);
        }
        return true;
    }

}
