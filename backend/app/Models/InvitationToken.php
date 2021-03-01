<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvitationToken extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at','created_at','updated_at','invite_until'];
    protected $guarded = [];
    const UPDATED_AT = null;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id','name');
    }

    public function inactive_once()
    {
        $this->invitation_times-=1;
        $this->invited+=1;
        $this->save();
        if(!$this->is_public&&$this->user&&$this->user->info){
            $this->user->info->increment('invitee_count');
        }
        return $this;
    }
}
