<?php

namespace Mix\Config;

/**
 * Class Environment
 * @package Mix\Config
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class Environment extends IniParser
{

    /**
     * 加载环境配置
     * @return bool
     */
    public function load()
    {
        if (!parent::load()) {
            throw new \Mix\Exceptions\ConfigException("Environment file does not exist: {$this->filename}");
        }
        $this->_data = array_merge($this->_data, $_SERVER, $_ENV);
        return true;
    }

}
