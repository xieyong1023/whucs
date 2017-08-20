<?php
/**
 *
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/19
 * @Time: 10:36
 */

use \Library\Core\Exception;
use PHPUnit\Framework\TestCase;

final class ExceptionTest extends TestCase
{
    public function dataProvider()
    {
        return [
            ['aaaa'],
            [null],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \Exception
     */
    public function testException($msg)
    {
        new Exception($msg);
    }
}