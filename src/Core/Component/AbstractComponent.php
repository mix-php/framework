<?php

namespace Mix\Core\Component;

use Mix\Core\Bean\AbstractObject;

/**
 * Class AbstractComponent
 * @package Mix\Core\Component
 * @author LIUJIAN <coder.keda@gmail.com>
 */
abstract class AbstractComponent extends AbstractObject implements ComponentInterface
{

    use ComponentTrait;

}
