<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class AdministrationCommand extends Model
{
    const UPDATED_AT = null;
    protected $guarded = [];
    protected $dates = ['created_at'];

    public function title()
    {
        return $this->belongsTo(Title::class, 'user_id');
    }

    public function scopeWithStatus($query, $status='')
    {
        if($status==="is_valid"){
            return $query->where('is_valid',1);
        }
        return $query;
    }

}
