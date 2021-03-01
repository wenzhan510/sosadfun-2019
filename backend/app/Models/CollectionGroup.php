<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon;

class CollectionGroup extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id','name','title_id');
    }

    public function update_count()
    {
        if($this->update_count>0){
            $this->update(['update_count' => 0]);
        }
    }

}
