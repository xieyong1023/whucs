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
use \Library\Tools\Validator\Validator;
use \Library\Tools\Validator\ValidatorProvider;

class DebugController extends BaseController
{
    /**
     * @var \Library\Log\Logger
     */
    protected $logger = null;
    /**
     * @var \Library\Database\Medoo
     */
    protected $medoo = null;

    public function indexAction()
    {
        echo 'debug';
    }

    public function testAction()
    {
        $this->disableView();

//        $this->medoo = DI::getInstance()->getShared('default_db');
//
//        $data = $this->medoo->update(
//            'test', ['id'=>1, 'username' => 'x', 'password' => 'a'], ['id' => 1]
//        );

        $a = new \Library\Tools\UserAgent();
        var_dump($a);

    }

    public function test(...$params)
    {

        var_dump($params);

    }
}
