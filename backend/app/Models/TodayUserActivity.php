<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TodayUserActivity extends Model
{
    protected $guarded = [];
    const UPDATED_AT = null;
    protected $dates = ['created_at'];

}
