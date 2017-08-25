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

        $this->db();
    }

    public function log()
    {
        $this->logger = DI::getInstance()->get('debug_log', ['seperator' => "\x1e"]);

//        var_dump($this->logger);
        $this->logger->error('hello');
    }

    public function db()
    {
        $db = new \Library\Database\DBBase();

//        $data = $this->medoo->update(
//            'test', ['id'=>1, 'username' => 'x', 'password' => 'a'], ['id' => 1]
//        );

        $user = [
            'username' => '111xy',
            'nickname' => '111xieyong',
            'password' => '1234',
            'student_id' => '123',
            'group_id' => 0,
            'create_time' => NOW_TIME,
            'update_time' => NOW_TIME,
            'status' => 0,
        ];
//        try{
            $data = $db->replace('user_core',['username' => '3333'], ['username' => '1111']);
//        } catch (\Exception $e) {
//            echo 'aa';
//        }

        var_dump($data);
    }
}
