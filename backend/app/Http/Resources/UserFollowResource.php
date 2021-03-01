<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserFollowResource extends JsonResource
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
            'type' => 'follower',
            'id' => (int)$this->id,
            'attributes' => [
                'user_id' => (int)$this->user_id,
                'follower_id' => (int)$this->follower_id,
                'keep_updated'=>(boolean)$this->keep_updated,
                'updated'=>(boolean)$this->updated,
            ],
            'user' => new UserBriefResource($this->whenLoaded('user_brief')),
            'follower' => new UserBriefResource($this->whenLoaded('follower_brief')),
        ];
    }
}
