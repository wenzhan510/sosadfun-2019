<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FirewallEmail extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function scopeEmailLike($query, $email)
    {
        if($email){
            return $query->where('email','like','%'.$email.'%');
        }
        return $query;
    }

}
