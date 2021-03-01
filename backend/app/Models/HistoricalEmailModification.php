<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricalEmailModification extends Model
{
    const UPDATED_AT = null;
    protected $guarded = [];
    protected $dates = ['created_at', 'old_email_verified_at', 'admin_revoked_at', 'email_changed_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id','name','title_id','level');
    }

    public function scopeEmailLike($query, $email)
    {
        if($email){
            return $query->where('old_email','like','%'.$email.'%')->orWhere('new_email','like','%'.$email.'%');
        }
        return $query;
    }

    public function scopeCreationIPLike($query, $ip)
    {
        if($ip){
            return $query->where('ip_address','like', $ip.'%');
        }
        return $query;
    }
}
