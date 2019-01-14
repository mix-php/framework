<?php

namespace Mix\Config;

use Mix\Core\BeanObject;

/**
 * Class IniParser
 * @package Mix\Config
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class IniParser extends BeanObject
{

    /**
     * 文件名
     * @var string
     */
    public $filename;

    /**
     * 数据
     * @var array
     */
    protected $_data = [];

    /**
     * 加载文件
     * @return bool
     */
    public function load()
    {
        if (!is_file($this->filename)) {
            return false;
        }
        $this->_data = parse_ini_file($this->filename, true);
        return true;
    }

    /**
     * 获取配置
     * @param $name
     * @param string $default
     * @return array|mixed|string
     */
    public function section($name, $default = '')
    {
        $current   = $this->_data;
        $fragments = explode('.', $name);
        foreach ($fragments as $key) {
            if (!isset($current[$key])) {
                return $default;
            }
            $current = $current[$key];
        }
        return $current;
    }

    /**
     * 返回全部数据
     * @return array
     */
    public function sections()
    {
        return $this->_data;
    }

}
