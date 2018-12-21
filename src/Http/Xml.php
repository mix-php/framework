<?php

namespace Mix\Http;

use Mix\Core\BaseObject;
use Mix\Helpers\XmlHelper;

/**
 * Xml类
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class Xml extends BaseObject
{

    // 编码
    public function encode($data)
    {
        return XmlHelper::encode($data);
    }

}
