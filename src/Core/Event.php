<?php

namespace Mix\Core;

/**
 * Class Event
 * @package Mix\Core
 * @author liu,jian <coder.keda@gmail.com>
 *
 * @method static add(mixed $sock, mixed $read_callback, mixed $write_callback = null, int $flags = null)
 * @method static set($fd, mixed $read_callback, mixed $write_callback, int $flags)
 * @method static isset(mixed $fd, int $events = SWOOLE_EVENT_READ | SWOOLE_EVENT_WRITE)
 * @method static write($fp, $data)
 * @method static exit()
 * @method static defer(mixed $callback_function)
 * @method static cycle(callable $callback, bool $before = false)
 * @method static wait()
 * @method static dispatch()
 */
class Event extends \Swoole\Event
{
}
