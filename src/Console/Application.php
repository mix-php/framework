<?php

namespace Mix\Console;

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
        if (Argument::subCommand() == '' && Argument::command() == '') {
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
        $command = trim(implode(' ', [Argument::command(), Argument::subCommand()]));
        $this->runAction($command);
    }

    // 帮助
    protected function help()
    {
        $input  = \Mix::$app->input;
        $output = \Mix::$app->output;
        $output->writeln("Usage: {$input->getScriptFileName()} [OPTIONS] COMMAND [SUBCOMMAND] [arg...]");
        $this->printOptions();
        $this->printCommands();
        $output->writeln('');
        $output->writeln("Developed with MixPHP framework.");
    }

    // 版本
    protected function version()
    {
        $input            = \Mix::$app->input;
        $output           = \Mix::$app->output;
        $appName          = \Mix::$app->appName;
        $appVersion       = \Mix::$app->appVersion;
        $frameworkVersion = \Mix::VERSION;
        $output->writeln("{$appName} version {$appVersion}, framework version {$frameworkVersion}");
    }

    // 打印选项列表
    protected function printOptions()
    {
        $output = \Mix::$app->output;
        $output->writeln('');
        $output->writeln('Options:');
        $output->writeln("  -h/--help\tPrint usage.");
        $output->writeln("  -v/--version\tPrint version information.");
    }

    // 打印命令列表
    protected function printCommands()
    {
        $output = \Mix::$app->output;
        $output->writeln('');
        $output->writeln('Commands:');
        $prevPrefix = '';
        foreach ($this->commands as $command => $item) {
            $prefix = explode(' ', $command)[0];
            if ($prefix != $prevPrefix) {
                $prevPrefix = $prefix;
                $output->writeln('  ' . $prefix);
            }
            $output->write(str_repeat(' ', 4) . $command, Output::FG_GREEN);
            $output->writeln((isset($item['description']) ? "\t{$item['description']}" : ''), Output::NONE);
        }
    }

    // 执行功能并返回
    public function runAction($command)
    {
        if (isset($this->commands[$command])) {
            // 实例化控制器
            list($shortClass, $shortAction) = $this->commands[$command];
            $shortClass    = str_replace('/', "\\", $shortClass);
            $commandDir    = \Mix\Helpers\FileSystemHelper::dirname($shortClass);
            $commandDir    = $commandDir == '.' ? '' : "$commandDir\\";
            $commandName   = \Mix\Helpers\FileSystemHelper::basename($shortClass);
            $commandClass  = "{$this->commandNamespace}\\{$commandDir}{$commandName}Command";
            $commandAction = "action{$shortAction}";
            // 判断类是否存在
            if (class_exists($commandClass)) {
                $commandInstance = new $commandClass($options);
                // 判断方法是否存在
                if (method_exists($commandInstance, $commandAction)) {
                    return $commandInstance->$commandAction();
                }
            }
        }
        throw new \Mix\Exceptions\NotFoundException("'{$command}' is not command, see '-h/--help'.");
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
