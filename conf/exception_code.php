<?php
/**
 * 异常码配置项，帮助快速确定异常码范围
 * 只记录，不在其他地方使用
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/26
 * @Time: 15:02
 */

return [
    // 异常类名   =>  [开始号码, 结束号码]
    'ConfigException'      => [100000, 101000],
    'DIException'          => [101000, 102000],
    'DBException'          => [102000, 103000],
    'ServiceException'     => [103000, 104000],
    'UserServiceException' => [104000, 105000],
];
