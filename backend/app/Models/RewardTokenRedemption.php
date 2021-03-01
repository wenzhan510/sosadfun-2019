<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardTokenRedemption extends Model
{
    const UPDATED_AT = null;
    protected $guarded = [];
    protected $dates = ['created_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id','name','title_id','level');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'token_creator_id');
    }
    public function creator_brief()
    {
        return $this->belongsTo(User::class, 'token_creator_id')->select('id','name','title_id','level');
    }
}
