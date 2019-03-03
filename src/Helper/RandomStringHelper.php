<?php

namespace Mix\Helper;

/**
 * RandomStringHelper类
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class RandomStringHelper
{

    // 获取随机字符
    public static function randomAlphanumeric($length)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
        $last  = 61;
        $str   = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars{mt_rand(0, $last)};
        }
        return $str;
    }

    // 获取随机字符
    public static function randomNumeric($length)
    {
        $chars = '1234567890';
        $last  = 9;
        $str   = '';
        for ($i = 0; $i < $length; $i++) {
            if ($i == 0) {
                $str .= $chars{mt_rand(0, $last - 1)};
            } else {
                $str .= $chars{mt_rand(0, $last)};
            }
        }
        return $str;
    }

}
