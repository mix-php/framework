<?php

namespace Mix\Console;

/**
 * Class Argument
 * @package Mix\Console
 * @author 刘健 <coder.liu@qq.com>
 */
class Argument
{

    /**
     * 获取脚本
     * @return string
     */
    public static function script()
    {
        $argv = $GLOBALS['argv'];
        return $argv[0];
    }

    /**
     * 获取命令
     * @return string
     */
    public static function command()
    {
        static $command;
        if (!isset($command)) {
            $argv    = $GLOBALS['argv'];
            $command = $argv[1] ?? '';
            $command = preg_match('/^[a-z0-9]+$/i', $command) ? $command : '';
        }
        return $command;
    }

    /**
     * 获取子命令
     * @return string
     */
    public static function subCommand()
    {
        if (self::command() == '') {
            return '';
        }
        static $subCommand;
        if (!isset($subCommand)) {
            $argv       = $GLOBALS['argv'];
            $subCommand = $argv[2] ?? '';
            $subCommand = preg_match('/^[a-z0-9]+$/i', $subCommand) ? $subCommand : '';
        }
        return $subCommand;
    }

}
