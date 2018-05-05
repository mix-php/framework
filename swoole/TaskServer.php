<?php

namespace mix\swoole;

use mix\base\BaseObject;

/**
 * 任务服务器类
 * @author 刘健 <coder.liu@qq.com>
 */
class TaskServer extends BaseObject
{

    // 左进程数
    public $leftProcess = 1;

    // 右进程数
    public $rightProcess = 3;

    // 服务名称
    public $name = '';

    // 进程队列的key
    public $queueKey = '';

    // 主进程pid
    protected $_masterPid = 0;

    // 工作进程pid集合
    protected $_workers = [];

    // 左进程启动事件回调函数
    protected $_onLeftStart;

    // 右进程启动事件回调函数
    protected $_onRightStart;

    // 启动
    public function start()
    {
        Process::setName("{$this->name}: task: master");
        $this->_masterPid = Process::getPid();
        $this->createLeftProcesses();
        $this->createRightProcesses();
        $this->subProcessWait();
    }

    // 注册Server的事件回调函数
    public function on($event, $callback)
    {
        switch ($event) {
            case 'LeftStart':
                $this->_onLeftStart = $callback;
                break;
            case 'RightStart':
                $this->_onRightStart = $callback;
                break;
        }
    }

    // 创建全部左进程
    protected function createLeftProcesses()
    {
        for ($i = 0; $i < $this->leftProcess; $i++) {
            $this->createProcess($i, $this->_onLeftStart, 'left');
        }
    }

    // 创建全部右进程
    protected function createRightProcesses()
    {
        for ($i = 0; $i < $this->rightProcess; $i++) {
            $this->createProcess($i, $this->_onRightStart, 'right');
        }
    }

    // 创建进程
    protected function createProcess($index, $callback, $processType)
    {
        if (!isset($callback)) {
            throw new \Exception('Create Process Error: ' . ($processType == 'left' ? '[LeftStart]' : '[RightStart]') . ' no callback.');
        }
        $process = new TaskProcess(function ($worker) use ($index, $callback, $processType) {
            try {
                Process::setName("{$this->name}: task: {$processType} #{$index}");
                list($object, $method) = $callback;
                $object->$method($worker, $index);
            } catch (\Exception $e) {
                \Mix::app()->error->exception($e);
            }
        }, false, false);
        $process->useQueue(crc32($this->queueKey), 2);
        $process->setMasterPid($this->_masterPid);
        $pid                  = $process->start();
        $this->_workers[$pid] = [$index, $callback, $processType];
        return $pid;
    }

    // 重启进程
    protected function rebootProcess($ret)
    {
        $pid = $ret['pid'];
        if (isset($this->_workers[$pid])) {
            list($index, $callback, $processType) = $this->_workers[$pid];
            $this->createProcess($index, $callback, $processType);
            return;
        }
        throw new \Exception('Reboot Process Error: no pid.');
    }

    // 回收结束运行的子进程，并重启子进程
    protected function subProcessWait()
    {
        while (true) {
            $ret = \Swoole\Process::wait();
            if ($ret) {
                $this->rebootProcess($ret);
            }
        }
    }

}
