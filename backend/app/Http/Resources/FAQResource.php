<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class FAQResource extends JsonResource
{
    /**
    * Transform the resource into an array.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return array
    */
    public function toArray($request)
    {
        return [
            'type' => 'faq',
            'id' => (int)$this->id,
            'attributes' => [
                'key' => (string)$this->key,
                'question' => (string)$this->question,
                'answer' => (string)$this->answer,
            ],
        ];
    }
}
