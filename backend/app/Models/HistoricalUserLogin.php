<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricalUserLogin extends Model
{
    protected $guarded = [];
    const UPDATED_AT = null;
    protected $dates = ['created_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id','name','title_id','level');
    }

}
