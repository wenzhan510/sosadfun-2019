<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $guarded = [];
    const UPDATED_AT = null;
    protected $dates = ['created_at'];

    public function user(){
    	return $this->belongsTo(User::class,'user_id');
    }
    public function votable(){
    	return $this->morphTo();
    }
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id','name','title_id','level');
    }
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id')->select('id','name','title_id','level');
    }
    public function scopeWithType($query, $type='')
    {
    	return $query->where('votable_type', '=', $type);
    }

    public function scopeWithId($query, $id=0)
    {
    	return $query->where('votable_id', '=', $id);
    }

    public function scopeWithAttitude($query, $attitude_type='')
    {
    	return $query->where('attitude_type', '=', $attitude_type);
    }
}
