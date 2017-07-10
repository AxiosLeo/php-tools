<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/5/16 15:55
 */
namespace example\index\validate;

use axios\tpr\core\Validate;

class Index extends Validate {
    protected $rule =   [
        'name'  => 'require|max:25',
    ];

    protected $message  =   [
        'name.require' => 'name@require',  //支持分段翻译,每段由@符号隔开
        'name.max'     => 'name@must be less than@25@char',
    ];

    protected $scene = [
        'test'  =>  ['name'],
    ];
}