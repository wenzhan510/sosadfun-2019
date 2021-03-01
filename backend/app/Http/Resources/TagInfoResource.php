<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TagInfoResource extends JsonResource
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
            'type' => 'tag',
            'id' => (int)$this->id,
            'attributes' => [
                'tag_name' => (string)$this->tag_name,
                'tag_explanation' => (string)$this->tag_explanation,
                'tag_type' => (string)$this->tag_type,
            ]
        ];
    }

}
