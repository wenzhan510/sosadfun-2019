<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizOption extends Model
{
    use Traits\DelayCountTrait;
    protected $guarded = [];
    const UPDATED_AT = 'edited_at';

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }
}
