<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Sosadfun\Traits\ColumnTrait;

class PostInfo extends Model
{
    use Traits\DelayCountTrait;

    protected $primaryKey = 'post_id';
    protected $guarded = [];
    public $timestamps = false;

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function reviewee()//被评论的文章
    {
        return $this->belongsTo(Thread::class, 'reviewee_id')->select('id','user_id','channel_id','title','brief','is_bianyuan','is_anonymous','is_public','no_reply','is_locked');
    }

    public function referer()//被附载的内容
    {
        return $this->morphTo('reviewee');
    }
}
