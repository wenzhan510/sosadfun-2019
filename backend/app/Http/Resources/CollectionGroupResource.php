<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CollectionGroupResource extends JsonResource
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
            'type' => 'collection_group',
            'id' => (int)$this->id,
            'attributes' => [
                'user_id' =>  (int)$this->user_id,
                'name' => (string)$this->name,
                'order_by' => $this->order_by,
            ],
        ];
    }
}
