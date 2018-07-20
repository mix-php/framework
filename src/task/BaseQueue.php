<?php

namespace mix\task;

use mix\base\BaseObject;

/**
 * 消息队列基类
 * @author 刘健 <coder.liu@qq.com>
 */
class BaseQueue extends BaseObject
{

    // 队列对象
    public $queue;

    // 进程对象
    public $worker;

    // 共享内存表
    public $table;

    // 投递数据
    public function push($data, $serialize = true)
    {
        if ($serialize) {
            $data = serialize($data);
        }
        return $this->queue->push($data);
    }

    // 提取数据
    public function pop($unserialize = true)
    {
        $data = $this->queue->pop();
        if ($unserialize && !empty($data)) {
            $data = unserialize($data);
        }
        return $data;
    }

    // 是否在左进程
    protected function isLeftWorker()
    {
        return $this->worker instanceof LeftWorker;
    }

    // 是否在左进程
    protected function isCenterWorker()
    {
        return $this->worker instanceof CenterWorker;
    }

    // 是否在右进程
    protected function isRightWorker()
    {
        return $this->worker instanceof RightWorker;
    }

    // 是否重启
    protected function isRestart()
    {
        return $this->table->get('signal', 'value') == TaskExecutor::SIGNAL_RESTART;
    }

}