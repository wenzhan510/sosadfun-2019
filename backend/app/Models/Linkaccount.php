<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Linkaccount extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function master()
    {
        return $this->belongsTo(User::class, 'master_account')->select('name');
    }
    public function branch()
    {
        return $this->belongsTo(User::class, 'branch_account')->select('name');
    }

}
