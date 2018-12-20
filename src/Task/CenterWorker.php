<?php

namespace Mix\Task;

/**
 * 工作者基类(中)
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class CenterWorker extends BaseWorker
{

    // 发送消息到右进程
    public function send($data)
    {
        return $this->outputQueue->push($data);
    }
    
}