<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TongrenResource extends JsonResource
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
            'type' => 'tongren',
            'id' => (int)$this->thread_id,
            'attributes' => [
                'thread_id' => (int)$this->thread_id,
                'tongren_yuanzhu' => (string)$this->tongren_yuanzhu,
                'tongren_CP' => (string)$this->tongren_CP,
            ]
        ];
    }
}
