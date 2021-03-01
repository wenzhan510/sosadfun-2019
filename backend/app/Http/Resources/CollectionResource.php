<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CollectionResource extends JsonResource
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
            'type' => 'collection',
            'id' => (int)$this->id,
            'attributes' => [
                'user_id' =>  (int)$this->user_id,
                'thread_id' => (int)$this->thread_id,
                'keep_updated' => (bool)$this->keep_updated,
                'group_id' => (int)$this->group_id,
                'last_read_post_id' => (int)$this->last_read_post_id,
            ],
            'thread' => new ThreadInfoResource($this->whenLoaded('briefThread')),
        ];
    }
}
