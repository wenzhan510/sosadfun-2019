<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminReviewQuote extends Model
{

    protected $guarded = [];
    const UPDATED_AT = null;
    protected $dates = ['created_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->select('id','name','title_id','level');
    }

    public function quote()
    {
        return $this->belongsTo(Quote::class, 'quote_id');
    }

}
