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
    public function indexAction()
    {
        echo 'debug';
    }

    public function testAction()
    {
        $this->disableView();

        $data = [
//            'foo' => 'value1',
            'bar' => 'value2',
        ];

        $rules = [
            'foo' => [
                'rules' => ['required'],
                'label' => '用户名',
            ],
            'bar'=> [
                'rules' => ['email', ['lengthMin', 4]],
//                'label' => '密码',
            ],
        ];


        $v = ValidatorProvider::buildValidator($data, $rules);

        if (! $v->validate()) {
            var_dump($v->errors());
        }
        var_dump($v);
    }

    public function test($a, $b)
    {

        var_dump($a, $b);

    }
}
