<?php

namespace Mix\Core;

/**
 * 通道钩子类
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class ChannelHook
{

    /**
     * @var \Swoole\Coroutine\Channel
     */
    protected $_channel;

    // 安装钩子
    public function install($channel)
    {
        $this->_channel = $channel;
    }

    // 处理钩子
    public function handle($e)
    {
        if ($this->_channel instanceof \Swoole\Coroutine\Channel) {
            return $this->_channel->push($e);
        }
        return false;
    }

}
