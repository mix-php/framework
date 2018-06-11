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
        if ($this->type == \mix\task\TaskExecutor::TYPE_DAEMON && !ProcessHelper::isRunning($this->mpid)) {
            $this->current->exit();
        }
        return true;
    }

    // 结束任务
    public function finish()
    {
        if ($this->type != \mix\task\TaskExecutor::TYPE_CRONTAB) {
            throw new \mix\exceptions\TaskException('LeftProcess Error: method \'finish\' is not available in TYPE_CRONTAB type.');
        }
        // 标记完成
        $this->table->set('leftFinishStatus', ['value' => 1]);
        // 退出
        $this->current->exit();
    }

}
