<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TitleResource extends JsonResource
{
    /**
    * Transform the resource into an array.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return array
    */
    public function toArray($request)
    {
        $is_public = '';
        if($this->pivot){
            $is_public = (bool)$this->pivot->is_public;
        }
        return [
            'type' => 'title',
            'id' => (int)$this->id,
            'attributes' => [
                'name' => (string)$this->name,
                'description' => (string)$this->description,
                'user_count' => (int)$this->user_count,
                'is_public' => $is_public,
                'style_id' => (int)$this->style_id,
                'type' => (string)$this->type,
                'level' => (int)$this->level,
                'style_type' => (string)$this->style_type,
            ]
        ];
    }
}
