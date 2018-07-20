<?php

namespace mix\task;

/**
 * 工作者基类(中)
 * @author 刘健 <coder.liu@qq.com>
 */
class CenterWorker extends BaseWorker
{

    // 发送消息到右进程
    public function send($data, $serialize = true)
    {
        return $this->outputQueue->push($data, $serialize);
    }
    
}