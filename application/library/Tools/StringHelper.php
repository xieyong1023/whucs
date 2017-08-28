<?php
/**
 * 字符串相关工具函数
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/26
 * @Time: 10:53
 */

namespace Library\Tools;

class StringHelper
{
    /**
     * 生成给定长度的随机字符串
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param int $length 字符串长度
     *
     * @return string
     */
    public static function getRandomString(int $length = 1)
    {
        $str = '';
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;

        for ($i = 0; $i < $length; $i++) {
            $str .= $strPol[rand(0, $max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }

        return $str;
    }
}
