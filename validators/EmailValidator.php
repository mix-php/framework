<?php

namespace mix\validators;

/**
 * EmailValidator类
 * @author 刘健 <coder.liu@qq.com>
 */
class EmailValidator extends BaseValidator
{

    // 启用的选项
    protected $_enabledOptions = ['length', 'minLength', 'maxLength'];

    // 类型验证
    protected function type()
    {
        $value = $this->attributeValue;
        if (!Validate::isEmail($value)) {
            // 设置错误消息
            $defaultMessage = "{$this->attribute}不符合邮箱格式.";
            $this->setError(__METHOD__, $defaultMessage);
            // 返回
            return false;
        }
        return true;
    }

}
