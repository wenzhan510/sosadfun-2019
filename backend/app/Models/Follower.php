<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user_brief()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id','name', 'title_id','level');
    }

    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    public function follower_brief()
    {
        return $this->belongsTo(User::class, 'follower_id')->select('id','name', 'title_id','level');
    }
}
