<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/5/16 14:40
 */
namespace axios\tpr\core;

use axios\tpr\service\LangService;
use think\Validate as ThinkValidate;

class Validate extends ThinkValidate{
    function __construct(array $rules = [], array $message = [], array $field = [])
    {
        parent::__construct($rules, $message, $field);
    }

    public function getError(){
        return LangService::trans($this->error);
    }
}