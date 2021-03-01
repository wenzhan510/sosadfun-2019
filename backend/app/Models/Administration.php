<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Administration extends Model
{
    use SoftDeletes;

    const UPDATED_AT = null;
    protected $guarded = [];
    protected $dates = ['created_at','deleted_at'];

    public function operator()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id','name', 'title_id');
    }

    public function scopeWithAdministratee($query, $id)
    {
        if($id>0){
            return $query->where('administratee_id','=',$id);
        }
        return $query;
    }

    public function scopeIsPublic($query, $is_public)
    {
        if($is_public=="include_private"){
            return $query;
        }
        return $query->where('is_public',1);
    }
}
