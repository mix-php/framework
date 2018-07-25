<?php

namespace mix\task;

/**
 * 输入消息队列
 * @author 刘健 <coder.liu@qq.com>
 */
class InputQueue extends BaseQueue
{

    // 投递数据
    public function push($data, $serialize = true)
    {
        // 重启信号处理
        if ($this->isLeftWorker() && ($this->isRestart() || $this->isStopLeft())) {
            if (parent::push($data, $serialize)) {
                $this->worker->exit();
            }
        }
        // 投递数据
        return parent::push($data, $serialize);
    }

    // 提取数据
    public function pop($unserialize = true)
    {
        // 重启信号处理
        if ($this->isCenterWorker() && ($this->isRestart() || $this->isStopAll())) {
            $this->worker->exit();
        }
        // 提取数据
        return parent::pop($unserialize);
    }

}