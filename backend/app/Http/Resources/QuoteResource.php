<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $author = [];
        if((!$this->is_anonymous)||((auth('api')->check())&&(auth('api')->id()===$this->user_id||auth('api')->user()->isAdmin()))){
            $author = new UserBriefResource($this->whenLoaded('author'));
        }

        return [
            'type' => 'quote',
            'id' => (int)$this->id,
            'attributes' => [
                'body' => (string)$this->body,
                'fish' => (int)$this->xianyu,
                'is_anonymous' => (bool)$this->is_anonymous,
                'majia' => (string)$this->majia,
            ],
            'author' => $author,
        ];
        // TODO 如果当前用户是管理员，返回quote里的其他内容，比如本题头是否通过等
    }
}
