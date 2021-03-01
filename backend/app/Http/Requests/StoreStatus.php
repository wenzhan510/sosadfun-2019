<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon;
use DB;
use App\Models\Status;
use StringProcess;
use App\Sosadfun\Traits\GenerateStatusDataTraits;

class StoreStatus extends FormRequest
{
    use GenerateStatusDataTraits;
    /**
    * Determine if the user is authorized to make this request.
    *
    * @return bool
    */
    public function authorize()
    {
        return true;
    }

    /**
    * Get the validation rules that apply to the request.
    *
    * @return array
    */
    public function rules()
    {
        return [
            'body' => 'required|string|max:1000',
            'reply_to_id' => 'numeric',
            'attachable_id' => 'numeric',
        ];
    }

    public function storeStatus()
    {
        $data = $this->generateStatusData();
        $data = $this->addReplyData($data);
        $data = $this->addAttachableData($data); 
        $status = Status::create($data);
        return $status;
    }

}
