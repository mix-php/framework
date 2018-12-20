<?php

namespace Mix\Http;

use Mix\Core\BaseObject;
use Mix\Helpers\JsonHelper;

/**
 * JSON 类
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class Json extends BaseObject
{

    // 编码
    public static function encode($data)
    {
        // 不转义中文、斜杠
        return JsonHelper::encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

}
