<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserBriefResource extends JsonResource
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
            'type' => 'user',
            'id' => (int)$this->id,
            'attributes' => [
                'name' => (string)$this->name,
                'level' => (string)$this->level,
                'title_id' => (string)$this->title_id,
            ],
            'title' => new TitleBriefResource($this->whenLoaded('title')),
        ];
    }
}
