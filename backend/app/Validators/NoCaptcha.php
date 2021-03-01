<?php
namespace App\Validators;

use ReCaptcha\ReCaptcha;

class NoCaptcha{

    public function validate($attribute, $value){
        $captcha = new ReCaptcha(env('NOCAPTCHA_SECRET'));
        $response = $captcha->verify($value, $_SERVER['REMOTE_ADDR']);
        return $response->isSuccess();
    }

    public function message($message, $attribute, $rule, $parameters){
        return trans('validation.recaptcha');
    }

}
