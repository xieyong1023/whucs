<?php
/**
 * Debug
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/17
 * @Time: 22:34
 */

use Library\DI\DI;
use Library\Core\BaseController;

class DebugController extends BaseController
{
    /**
     * @var \Library\Log\Logger
     */
    protected $logger = null;
    public function indexAction()
    {
        echo 'debug';
    }

    public function testAction()
    {
        $this->disableView();

        $a = new \Library\Mysql\Test();

        var_dump($a->getOne());
    }

    public function test(...$msg)
    {
        var_dump(implode(',', $msg));
        var_dump($msg);
    }
}
