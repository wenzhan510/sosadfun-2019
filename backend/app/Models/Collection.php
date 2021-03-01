<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id','name','title_id');
    }

    public function thread()//收藏的对象
    {
        return $this->belongsTo(Thread::class, 'thread_id');
    }

    public function briefThread()//收藏的对象
    {
        return $this->belongsTo(Thread::class, 'thread_id')->brief();
    }

    public function group()//从属的收藏页
    {
        return $this->belongsTo(CollectionGroup::class, 'group_id');
    }

}
