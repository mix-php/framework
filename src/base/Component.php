<?php

namespace mix\base;

/**
 * 组件基类
 * @author 刘健 <coder.liu@qq.com>
 */
abstract class Component implements BaseObjectInterface, ComponentInterface
{

    use BaseObjectTrait, ComponentTrait;

}
