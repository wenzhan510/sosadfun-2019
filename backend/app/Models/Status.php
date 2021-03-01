<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Status extends Model
{
    use SoftDeletes;

    use Traits\VoteTrait;
    use Traits\RewardTrait;
    use Traits\TypeValueChangeTrait;

    const UPDATED_AT = null;

    protected $dates = ['deleted_at','created_at'];
    protected $guarded = [];
    protected $count_types = ['upvote_count','forward_count','reply_count'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id','name','title_id','level');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    public function last_reply()
    {
        return $this->belongsTo(Status::class, 'last_reply_id')->select(['id', 'user_id', 'brief', 'created_at']);
    }

    public function parent()
    {
        return $this->belongsTo(Status::class, 'reply_to_id');
    }

    public function replies()
    {
        return $this->hasMany(Status::class, 'reply_to_id');
    }

    public function attachable(){
    	return $this->morphTo();
    }

    public function scopeOrdered($query, $ordered='')
    {
        switch ($ordered) {
            case 'earliest_created'://最老
            return $query->orderBy('created_at', 'desc');
            break;
            default://默认按时间顺序排列，返回最新
            return $query->orderBy('created_at', 'desc');
        }
    }

    public function scopeLaterThen($query, $laterThen='')
    {
        if($laterThen){
            return $query->where('created_at', '>', $laterThen);
        }
        return $query;
    }

    public function scopeIsPublic($query)
    {
        return $query->where('is_public',1);
    }
    public function scopeIsDirect($query)
    {
        return $query->where('reply_to_id',0);
    }

    public function scopeWithUser($query, $id)
    {
        return $query->where('user_id',$id);
    }

    public function scopeHasFollower($query, $id)
    {
        $query = $query->whereHas('followers', function ($query) use ($id){
            $query->where('followers.follower_id', '=', $id);
        });
        return $query;
    }

    public function latest_rewards()
    {
        return \App\Models\Reward::with('author')
        ->withType('status')
        ->withId($this->id)
        ->latest()
        ->take(10)
        ->get();
    }

    public function latest_upvotes()
    {
        return \App\Models\Vote::with('author')
        ->withType('status')
        ->withId($this->id)
        ->withAttitude('upvote')
        ->orderBy('created_at','desc')
        ->take(10)
        ->get();
    }

    public function reward_check(){
        $msg = '';
        if(!$this->reply_to_id&&!$this->attachable_id){
            $this->user->reward('regular_status');
            $msg = $msg.', 得到了动态奖励';
        }
        return '恭喜，你成功发布动态，缓存数分钟后会在讨论主题内展示'.$msg;
    }


}
