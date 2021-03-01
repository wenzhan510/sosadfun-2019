<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Tag extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    const UPDATED_AT = null;
    protected $dates = ['created_at', 'deleted_at'];

    public function threads()
    {
        return $this->belongsToMany('App\Models\Thread', 'tag_thread', 'tag_id', 'thread_id');
    }

    public function posts()
    {
        return $this->belongsToMany('App\Models\Post', 'tag_post', 'tag_id', 'post_id');
    }

    public function parent()
    {
        return $this->belongsTo(Tag::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(Tag::class, 'parent_id');
    }
    public function admin_only()//判断这个tag是否可以被用户自己控制
    {
        return in_array($this->tag_type, config('tag.limits.admin_only'));
    }
    public function channel()
    {
        return collect(config('channel'))->keyby('id')->get($this->channel_id);
    }
    public function scopeBrief($query){
        return $query->select('id','tag_name','tag_type');
    }
}
