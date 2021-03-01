<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserInfoResource extends JsonResource
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
            'type' => 'user_info',
            'id' => (int)$this->user_id,
            'attributes' => [
                'brief_intro' => (string)$this->brief_intro,
                'salt' => (int)$this->salt,
                'fish' => (int)$this->fish,
                'ham' => (int)$this->ham,
                'follower_count' => (int)$this->follower_count,
                'following_count' => (int)$this->following_count,
                'qiandao_max' => (int)$this->qiandao_max,
                $this->mergeWhen(auth('api')->check() && (auth('api')->user()->isAdmin()||auth('api')->id()===$this->user_id), [ // 这部分是仅自己或管理可见的
                    'qiandao_continued' => (int)$this->qiandao_continued,
                    'qiandao_all' => (int)$this->qiandao_all,
                    'qiandao_last' => (int)$this->qiandao_last,
                    'qiandao_at' => Carbon::parse($this->qiandao_at)->diffForHumans(),
                    'register_at' => Carbon::parse($this->user->created_at)->diffForHumans(),
                    'invitor_id' => (int)$this->invitor_id,
                    'token_limit' => (int)$this->token_limit,
                    'donation_level' => (int)$this->donation_level,
                    'qiandao_reward_limit' => (int)$this->qiandao_reward_limit,
                    // TODO email_verified_at
                ]),
                $this->mergeWhen(auth('api')->check() && auth('api')->user()->isAdmin(), [ // 这部分是仅管理可见的
                    // TODO
                    // collection 相关统计collection_total_count etc.
                    // collection_ip
                ]),
            ],
        ];
    }
}
