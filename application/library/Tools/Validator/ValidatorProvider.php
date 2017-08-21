<?php
/**
 * Validator工厂类
 *
 * @author: xieyong <qxieyongp@163.com>
 * @date: 2017/8/21
 * @time: 13:30
 * @see https://github.com/vlucas/valitron
 */

namespace Library\Tools\Validator;

/**
 * Class ValidatorProvider
 * @package Library\Tools\Validator
 */
class ValidatorProvider
{
    /**
     * @var string 语言(只与验证器有关)
     */
    protected static $lang = 'zh-cn';
    /**
     * @var string 语言文件地址
     */
    protected static $lang_dir = __DIR__ . '/ValidatorLang';

    /**
     * 构建验证器，这里只用到了一种设置规则
     *
     * 输入数据：
     * $data = [
     *     'foo' => 'value1',
     * ];
     *
     * 验证规则
     * $rules = [
     *     'foo' => [
     *              'rules' => ['required', 'integer'],  // 规则
     *              'label' => '别名1', // 别名
     *          ],
     * ];
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param array $data 数据
     * @param array $rules 规则
     *
     * @return Validator
     */
    public static function buildValidator(array $data, array $rules)
    {

        $validator = new Validator($data, [], self::$lang, self::$lang_dir);

        if (! empty($rules)) {
            $rules_array = [];
            $labels_array = [];

            foreach ($rules as $field_name => $param) {
                if (! empty($param['rules'])) {
                    $rules_array[$field_name] = $param['rules'];
                }

                if (! empty($param['label'])) {
                    $labels_array[$field_name] = $param['label'];
                }
            }
        }

//        var_dump($rules_array);
//        var_dump($labels_array);die;
        $validator->mapFieldsRules($rules_array);
        $validator->labels($labels_array);

        return $validator;
    }
}
