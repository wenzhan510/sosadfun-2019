<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RewardToken extends Model
{
    use SoftDeletes;
    const UPDATED_AT = null;
    protected $guarded = [];
    protected $dates = ['created_at', 'deleted_at', 'redeem_until'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id','name','title_id','level');
    }

    public function scopeIsRedeemable($query)
    {
        return $query->where('redeem_limit','>',0)->where('redeem_until','>',Carbon::now());;
    }

    public function scopeIsPublic($query)
    {
        return $query->where('is_public',1);
    }

    public function scopeIsPrivate($query)
    {
        return $query->where('is_public',0);
    }
}
