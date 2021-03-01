<?php
namespace App\Validators;

class DisplayLength{

    private $persistant_info = 'Foo';

    // the following code comes from GitHub: https://github.com/zhiyicx/thinksns-plus

    public function validate($attribute, $value, $parameters, $validator){
        $this->persistant_info = 'Bar';

        if (empty($parameters)) {
            throw new \InvalidArgumentException('Parameters must be passed');
        }

        $min = 0;
        if (count($parameters) === 1) {
            list($max) = $parameters;
        } elseif (count($parameters) >= 2) {
            list($min, $max) = $parameters;
        }

        if (! isset($max) || $max < $min) {
            throw new \InvalidArgumentException('The parameters passed are incorrect');
        }

        // 计算单字节.
        preg_match_all('/[a-zA-Z0-9_\s\,\-\.]/', $value, $single);
        $single = count($single[0]) / 2;

        // 多子节长度.
        $double = mb_strlen(preg_replace('([a-zA-Z0-9_\s\,\-\.])', '', $value));

        $length = $single + $double;

        return $length >= $min && $length <= $max;
    }

    public function message($message, $attribute, $rule, $parameters){
        return $this->persistant_info; //returns Bar
    }

}
