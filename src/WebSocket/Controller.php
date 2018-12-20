<?php

namespace Mix\WebSocket;

use Mix\Core\BaseObject;

/**
 * Controller类
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class Controller extends BaseObject
{

    /**
     * 服务
     * @var \Swoole\WebSocket\Server
     */
    public $server;

    // 文件描述符
    public $fd;

}
