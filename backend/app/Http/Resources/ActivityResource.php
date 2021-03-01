<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class ActivityResource extends JsonResource
{
    /**
    * Transform the resource into an array.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return array
    */
    public function toArray($request)
    {
        $item = null;
        if($this->item_type==='post'){
            $item = new PostBriefResource($this->whenLoaded('item'));
        }
        if($this->item_type==='status'){
            $item = new StatusResource($this->whenLoaded('item'));
        }
        if($this->item_type==='quote'){
            $item = new QuoteResource($this->whenLoaded('item'));
        }
        if($this->item_type==='thread'){
            $item = new ThreadBriefResource($this->whenLoaded('item'));
        }
        return [
            'type' => 'activity',
            'id' => (int)$this->id,
            'attributes' => [
                'kind' => (int) $this->kind,
                'seen' => (boolean) $this->seen,
                'item_id' => (int) $this->item_id,
                'item_type' => (string) $this->item_type,
                'user_id' => (int) $this->user_id,
            ],
            'item' => $item,
            'owner' => new UserBriefResource($this->whenLoaded('owner')),
        ];
    }

}
