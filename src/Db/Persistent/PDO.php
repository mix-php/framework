<?php

namespace Mix\Db\Persistent;

/**
 * PdoPersistent组件
 * @author 刘健 <coder.liu@qq.com>
 */
class PDO extends BasePDO
{

    // 析构事件
    public function onDestruct()
    {
        parent::onDestruct();
        // 关闭连接
        $this->disconnect();
    }

}
