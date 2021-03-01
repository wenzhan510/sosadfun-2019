<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;

class ConvertStringBooleans extends TransformsRequest
{
    protected function transform($key, $value)
    {
        if($value === 'true')
            return true;

        if($value === 'false')
            return false;

        return $value;
    }
}
