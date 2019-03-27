<?php

namespace Mix\Core\Coroutine;

/**
 * Class Channel
 * @package Mix\Core\Coroutine
 * @author liu,jian <coder.keda@gmail.com>
 *
 * @method bool push($data)
 * @method mixed pop()
 * @method bool isEmpty()
 * @method bool isFull()
 * @method array stats()
 * @method int length()
 * @method close()
 */
class Channel extends \Swoole\Coroutine\Channel
{
}
