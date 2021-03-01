<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon;

class UserIntro extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'user_id';

    protected $dates = ['edited_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
