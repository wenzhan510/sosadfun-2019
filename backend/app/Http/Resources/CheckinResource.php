<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CheckinResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    // TODO: Honestly I think this Resource is unnecessary, as the data is already in array form
    // but probably having all response object defined in Resource file is easier for other ppl.
    // let me know if we should delete this file. -- Emol. 03/01/2020
    public function toArray($request)
    {
        $checkin_reward = $this['checkin_reward'];

        return [
            'type' => 'checkin',
            'attributes' => [
                'levelup' => (boolean)$this['levelup'],
                'checkin_reward' => [
                    'special_reward' => (boolean)$checkin_reward['special_reward'],
                    'salt' => (int)$checkin_reward['salt'],
                    'fish' => (int)$checkin_reward['fish'],
                    'ham' => (int)$checkin_reward['ham'],
                ]
            ],
            'info' => new UserInfoResource($this['user_info']),
        ];
    }
}
