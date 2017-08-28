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

        \Library\Tools\Session::getInstance()->set('hello', 'world');
        var_dump($_SESSION);
        var_dump(session_status());
        \Library\Tools\Session::getInstance()->set('aiyo', 'oo');
        $data = \Library\Tools\Session::getInstance()->get('hello');
        var_dump(session_status());
        $data = \Library\Tools\Session::getInstance()->get('aiyo');
        var_dump($data);

        \Library\Tools\Session::getInstance()->delete('hello');
        var_dump($_SESSION);
//        \Library\Tools\Session::getInstance()->destroy();
    }

    public function test1Action()
    {
        $this->disableView();
        \Library\Tools\Session::getInstance()->destroy();

        var_dump(ini_get('session.cookie_path'));
    }

    public function log()
    {
        $this->logger = DI::getInstance()->get('debug_log', ['seperator' => "\x1e"]);

//        var_dump($this->logger);
        $this->logger->error('hello');
    }

    public function db()
    {
        $db = new \Library\Mysql\UserCore();

//        $data = $this->medoo->update(
//            'test', ['id'=>1, 'username' => 'x', 'password' => 'a'], ['id' => 1]
//        );

        $user = [
            'username'    => '111xy',
            'nickname'    => '111xieyong',
            'password'    => '1234',
            'student_id'  => '123',
            'group_id'    => 0,
            'create_time' => NOW_TIME,
            'update_time' => NOW_TIME,
            'status'      => 0,
        ];

//        $data = $db->insert($user);
        $data = $db->getAttribute();
        var_dump($data);
    }

    public function randString()
    {
        var_dump(\Library\Tools\StringHelper::getRandomString(10));
    }

    public function cookie()
    {

//        $cookie->setCookie('test', '123456');
//        $cookie->setCookie('test', 'qwer');
//        $cookie->deleteCookie('test');

    }
}
