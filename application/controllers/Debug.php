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

    protected $a = [
        'a' => 123,
    ];

    public function indexAction()
    {
        echo 'debug';
    }

    public function testAction()
    {
        $this->disableView();



    }

    public function log()
    {
        $this->logger = DI::getInstance()->get('debug_log', ['seperator' => "\x1e"]);

//        var_dump($this->logger);
        $this->logger->error('hello');
    }

    public function db()
    {
        //        $this->medoo = DI::getInstance()->getShared('default_db');
//
//        $data = $this->medoo->update(
//            'test', ['id'=>1, 'username' => 'x', 'password' => 'a'], ['id' => 1]
//        );

//        var_dump(\Library\Tools\Encrypt::encrypt('abcd', 'ENCODE', 'e66c63606d6179dd25e93ddd87d38c89'));}
    }
}
