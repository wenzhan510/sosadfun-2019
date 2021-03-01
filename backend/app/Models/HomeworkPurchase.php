<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeworkPurchase extends Model
{
    protected $guarded = [];
    protected $dates = ['created_at'];
    const UPDATED_AT = null;

    public function homework()
    {
        return $this->belongsTo(Homework::class, 'homework_id');
    }
    
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id')->select(['id','name']);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
