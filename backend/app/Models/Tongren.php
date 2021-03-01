<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Tongren extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = 'thread_id';

    public function thread()
    {
        return $this->belongsTo(Thread::class, 'thread_id');
    }
}
