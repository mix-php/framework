<?php

namespace Mix\Database\Persistent;

/**
 * PdoPersistent组件
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class PDOConnection extends BasePDOConnection
{

    // 析构事件
    public function onDestruct()
    {
        parent::onDestruct();
        // 关闭连接
        $this->disconnect();
    }

}
