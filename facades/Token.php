<?php

namespace mix\facades;

use mix\base\Facade;

/**
 * Token 门面类
 * @author 刘健 <coder.liu@qq.com>
 *
 * @method set($name, $value) static
 * @method setUniqueIndex($uniqueId) static
 * @method get($name = null) static
 * @method has($name) static
 * @method delete($name) static
 * @method clear() static
 * @method getTokenId() static
 */
class Token extends Facade
{

    // 获取实例
    public static function getInstance()
    {
        return \Mix::app()->token;
    }

}
