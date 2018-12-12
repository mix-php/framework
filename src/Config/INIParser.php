<?php

namespace Mix\Config;

/**
 * Class INIParser
 * @package Mix\Config
 * @author 刘健 <coder.liu@qq.com>
 */
class INIParser
{

    // 数据
    protected $_data = [];

    // 加载文件
    public function load($file)
    {
        if (!is_file($file)) {
            throw new \Mix\Exceptions\NotFoundException("INI file does not exist: {$file}.");
        }
        $this->_data = parse_ini_file($file, true);
    }

    // 获取配置
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

    // 返回全部数据
    public function sections()
    {
        return $this->_data;
    }

}
