<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use Traits\DelayCountTrait;
    const UPDATED_AT = 'edited_at';

    protected $guarded = [];

    public function quiz_options()
    {
        return $this->hasMany(QuizOption::class, 'quiz_id')->orderBy('id', 'asc');
    }
    public function random_options()
    {
        return $this->hasMany(QuizOption::class, 'quiz_id')->inRandomOrder();
    }
    public function scopeWithQuizType($query, $type = '')
    {
        if(array_key_exists($type, config('constants.quiz_types'))){
            return $query->where('type',$type);
        }
        if($type==='off_line'){
            return $query->where('is_online', 0);
        }
        return $query;
    }
    public function scopeWithQuizTypeRange($query, $type = '')
    {
        $type = explode(',',$type);
        return $query->whereIn('type',$type);
    }
    public function scopeIsOnline($query)
    {
        return $query->where('is_online', 1);
    }
    public function scopeWithQuizOnlineStatus($query, $online_status='online')
    {
        if ($online_status == 'either') {
            return $query;
        } elseif ($online_status == 'offline') {
            return $query->where('is_online', 0);
        } else {
            return $query->where('is_online', 1);
        }
    }
    public function scopeWithQuizLevel($query, $level)
    {
        if(is_numeric($level)&&$level>=0){
            return $query->where('quiz_level',$level);
        }
        return $query;
    }
    public function scopeWithQuizLevelRange($query, $level_range)
    {
        $level_range = explode(',',$level_range);
        foreach ($level_range as $index => $level) {
            if (!is_numeric($level) || $level<0) {
                unset($level_range[$index]);
            }
        }
        if(count($level_range) > 0){
            return $query->whereIn('quiz_level',$level_range)->orWhereNotIn('type',config('constants.quiz_has_level'));
        }
        return $query;
    }
}
