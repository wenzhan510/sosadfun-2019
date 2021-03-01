<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageBodyResource extends JsonResource
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
            'type' => 'message_body',
            'id' => (int)$this->id,
            'attributes' => [
                'body' => (string)$this->body,
                'bulk' => (bool)$this->bulk,
            ],
        ];
    }
}
