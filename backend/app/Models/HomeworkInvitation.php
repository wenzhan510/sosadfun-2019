<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeworkInvitation extends Model
{
    protected $guarded = [];
    const UPDATED_AT = null;
    protected $dates = ['created_at', 'valid_until'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id')->select(['id','name']);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function homework()
    {
        return $this->belongsTo(Homework::class, 'homework_id');
    }

    public function scopeWithRole($query, $withRole='')
    {
        if($withRole){
            return $query->whereIn('role', [$withRole, null]);
        }
        return $query;
    }

    public function scopeWithLevel($query, $withLevel=0)
    {
        if(is_numeric($withLevel)){
            return $query->where('level', '>=',$withLevel);
        }
        return $query;
    }

    public function scopeWithHomework($query, $withHomework=0)
    {
        if(is_numeric($withHomework)&&$withHomework>0){
            return $query->whereIn('homework_id', [$withHomework, 0]);
        }
        return $query->where('homework_id', 0);
    }

}
