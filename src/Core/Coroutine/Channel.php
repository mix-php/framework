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

    /**
     * Channel constructor.
     * 为了代码补全才加的，@method无法设置构造方法
     * @param int $capacity
     */
    public function __construct(int $capacity = 1)
    {
        parent::__construct($capacity);
    }

}
