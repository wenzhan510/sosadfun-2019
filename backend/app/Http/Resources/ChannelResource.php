<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChannelResource extends JsonResource
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
                'channel_explanation' => (string)$this->channel_explanation,
                'order_by' => (int)$this->order_by,
                'channel_type' => (string)$this->type,
                'allow_anonymous' => (bool)$this->allow_anonymous,
                'allow_edit' => (bool)$this->allow_edit,
                'is_public' => (bool)$this->is_public,
            ]
        ];
    }
}
