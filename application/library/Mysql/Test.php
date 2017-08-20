<?php
/**
 * Test表
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/20
 * @Time: 16:09
 */

namespace Library\Mysql;

use \Library\Database\DBBase;
use Library\Database\Medoo;
use Library\DI\DI;

class Test extends DBBase
{
    protected $config_name = 'default';

    protected $table = 'test';
}
