<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChannelBriefResource extends JsonResource
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
            'type' => 'channel',
            'id' => (int)$this->id,
            'attributes' => [
                'channel_name' => (string)$this->channel_name,
            ]
        ];
    }
}
