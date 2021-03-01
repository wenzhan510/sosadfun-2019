<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Sosadfun\Traits\ColumnTrait;

class HistoricalPasswordReset extends Model
{
    protected $table='historical_password_resets';
    protected $guarded = [];
    protected $primaryKey = 'id';
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
