<?php

namespace App\Http\Requests;

use App\Http\Requests\FormRequest;
use App\Models\Quote;
use DB;

class StoreQuote extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth('api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'body' => 'required|string|max:80|unique:quotes',
            'majia' => 'string|max:10',
        ];
    }

    public function generateQuote()
    {
        $quote_data = $this->only('body');
        $quote_data['user_id'] = auth('api')->id();
        if ($this->is_anonymous){
            $quote_data['is_anonymous'] = 1;
            $quote_data['majia'] = $this->majia;
        } else{
            $quote_data['is_anonymous'] = 0;
        }
        $quote = Quote::create($quote_data);
        return $quote;
    }
}
