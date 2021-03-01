<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use SoftDeletes;
    use Traits\VoteTrait;
    use Traits\RewardTrait;
    use Traits\TypeValueChangeTrait;

    protected $guarded = [];
    const UPDATED_AT = null;
    protected $dates = ['created_at', 'deleted_at'];
    protected $count_types = array('fish');

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id','name','title_id','level');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->select('id','name', 'title_id');
    }

    public function admin_reviews()
    {
        return $this->hasMany(AdminReviewQuote::class,'quote_id');
    }

    public function scopeOrdered($query, $ordered="")
    {
        switch ($ordered) {
            case 'earliest_created'://创建时间
            return $query->orderBy('created_at', 'asc');
            case 'latest_created'://创建时间
            return $query->orderBy('created_at', 'desc');
            break;
            case 'id'://创建顺序
            return $query->orderBy('id', 'asc');
            break;
            case 'max_fish'://被投咸鱼数
            return $query->orderBy('fish', 'desc');
            break;
            default://默认
            return $query->orderBy('created_at', 'desc');
        }
    }

    public function scopeNotSad($query)
    {
        return $query->where('notsad','=',true);
    }
    public function scopeWithReviewState($query, $state = '')
    {
        if($state==='AdminReview30'){
            $query = $query->where('review_count', 3)->where('pass_count', 3);
        }
        if($state==='AdminReview21'){
            $query = $query->where('review_count', 3)->where('pass_count', 2);
        }
        if($state==='AdminReview12'){
            $query = $query->where('review_count', 3)->where('pass_count', 1);
        }
        if($state==='AdminReview03'){
            $query = $query->where('review_count', 3)->where('pass_count', 0);
        }

        if($state==='notYetReviewed'){
            $query = $query->where('reviewed','=',0);
        }
        if($state==='Reviewed'){
            $query = $query->where('reviewed','=',1);
        }
        if($state==='Passed'){
            $query = $query->where('approved','=',1);
        }
        if($state==='UnPassed'){
            $query = $query->where('approved','=',0)->where('reviewed','=',1);
        }
        return $query;
    }

    public function latest_rewards()
    {
        return \App\Models\Reward::with('author')
        ->withType('quote')
        ->withId($this->id)
        ->orderBy('created_at','desc')
        ->take(10)
        ->get();
    }

}
