<?php

namespace Mix\Log;

/**
 * Interface HandlerInterface
 * @package Mix\Core
 * @author LIUJIAN <coder.keda@gmail.com>
 */
interface HandlerInterface
{

    // 写入日志
    public function write($level, $message, array $context = []);

}
