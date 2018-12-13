<?php

namespace Mix\Console;

use Mix\Console\CommandLine\Arguments;
use Mix\Console\CommandLine\Flag;

/**
 * App类
 * @author 刘健 <coder.liu@qq.com>
 */
class Application extends \Mix\Core\Application
{

    // 应用名称
    public $appName = 'app-console';

    // 应用版本
    public $appVersion = '0.0.0';

    // 命令命名空间
    public $commandNamespace = '';

    // 命令
    public $commands = [];

    // 执行功能 (CLI模式)
    public function run()
    {
        if (PHP_SAPI != 'cli') {
            throw new \RuntimeException('Please run in CLI mode.');
        }
        Flag::initialize();
        if (Arguments::subCommand() == '' && Arguments::command() == '') {
            if (Flag::bool(['h', 'help'], false)) {
                $this->help();
                return;
            }
            if (Flag::bool(['v', 'version'], false)) {
                $this->version();
                return;
            }
            $options = Flag::options();
            if (empty($options)) {
                $this->help();
                return;
            }
            $keys = array_keys($options);
            $flag = array_shift($keys);
            throw new \Mix\Exceptions\NotFoundException("flag provided but not defined: '{$flag}', see '-h/--help'.");
        }
        $command = trim(implode(' ', [Arguments::command(), Arguments::subCommand()]));
        $this->runAction($command);
    }

    // 帮助
    protected function help()
    {
        $script = Arguments::script();
        println("Usage: {$script} [OPTIONS] COMMAND [SUBCOMMAND] [arg...]");
        $this->printOptions();
        $this->printCommands();
        println('');
        println("Developed with MixPHP framework.");
    }

    // 版本
    protected function version()
    {
        $appName          = \Mix::$app->appName;
        $appVersion       = \Mix::$app->appVersion;
        $frameworkVersion = \Mix::VERSION;
        println("{$appName} version {$appVersion}, framework version {$frameworkVersion}");
    }

    // 打印选项列表
    protected function printOptions()
    {
        println('');
        println('Options:');
        println("  -h/--help\tPrint usage.");
        println("  -v/--version\tPrint version information.");
    }

    // 打印命令列表
    protected function printCommands()
    {
        println('');
        println('Commands:');
        $prevPrefix = '';
        foreach ($this->commands as $command => $item) {
            $prefix = explode(' ', $command)[0];
            if ($prefix != $prevPrefix) {
                $prevPrefix = $prefix;
                println('  ' . $prefix);
            }
            println(str_repeat(' ', 4) . $command . (isset($item['description']) ? "\t{$item['description']}" : ''));
        }
    }

    // 执行功能并返回
    public function runAction($command)
    {
        if (!isset($this->commands[$command])) {
            throw new \Mix\Exceptions\NotFoundException("'{$command}' is not command, see '-h/--help'.");
        }
        // 实例化控制器
        $shortClass    = $this->commands[$command];
        $shortClass    = str_replace('/', "\\", $shortClass);
        $commandDir    = \Mix\Helpers\FileSystemHelper::dirname($shortClass);
        $commandDir    = $commandDir == '.' ? '' : "$commandDir\\";
        $commandName   = \Mix\Helpers\FileSystemHelper::basename($shortClass);
        $commandClass  = "{$this->commandNamespace}\\{$commandDir}{$commandName}Command";
        $commandAction = 'main';
        // 判断类是否存在
        if (!class_exists($commandClass)) {
            throw new \Mix\Exceptions\CommandException("'{$commandClass}' class not found.");
        }
        $commandInstance = new $commandClass();
        // 判断方法是否存在
        if (!method_exists($commandInstance, $commandAction)) {
            throw new \Mix\Exceptions\CommandException("'{$commandClass}::main' method not found.");
        }
        return $commandInstance->$commandAction();
    }

    // 获取组件
    public function __get($name)
    {
        // 从容器返回组件
        return $this->container->get($name);
    }

    // 打印变量的相关信息
    public function dump($var, $send = false)
    {
        static $content = '';
        ob_start();
        var_dump($var);
        $dumpContent = ob_get_clean();
        $content     .= $dumpContent;
        if ($send) {
            throw new \Mix\Exceptions\DebugException($content);
        }
    }

    // 终止程序
    public function end($code = 0)
    {
        throw new \Mix\Exceptions\EndException('', $code);
    }

}
