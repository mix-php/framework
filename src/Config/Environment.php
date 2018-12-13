<?php

namespace Mix\Config;

/**
 * Class Environment
 * @package Mix\Config
 * @author 刘健 <coder.liu@qq.com>
 */
class Environment
{

    /**
     * 数据
     * @var array
     */
    protected static $_data = [];

    /**
     * 加载环境配置
     * @param $file
     */
    public static function load($file)
    {
        $iniParser = new INIParser(['filename' => $file]);
        if (!$iniParser->load()) {
            throw new \Mix\Exceptions\NotFoundException("Environment file does not exist: {$file}.");
        }
        $data        = $iniParser->sections();
        self::$_data = array_merge($data, $_SERVER, $_ENV);
    }

    /**
     * 获取配置
     * @param $name
     * @param string $default
     * @return array|mixed|string
     */
    public static function section($name, $default = '')
    {
        $current   = self::$_data;
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
    public static function sections()
    {
        return self::$_data;
    }

}
