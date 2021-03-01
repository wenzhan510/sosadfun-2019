<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Volumn extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    public function thread()
    {
        return $this->belongsTo(Thread::class, 'thread_id');
    }
}
