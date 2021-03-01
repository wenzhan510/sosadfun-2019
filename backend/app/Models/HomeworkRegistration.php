<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeworkRegistration extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $dates = ['registered_at', 'submitted_at', 'finished_at'];

    public function homework()
    {
        return $this->belongsTo(Homework::class, 'homework_id');
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class, 'thread_id');
    }

    public function required_critique_thread()
    {
        return $this->belongsTo(Thread::class, 'required_critique_thread_id')->brief();
    }

    public function briefThread()
    {
        return $this->belongsTo(Thread::class, 'thread_id')->brief();
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id')->select(['id','name']);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeHomeworkActive($query, $withHomeworkActive='')
    {
        if($withHomeworkActive==='is_active'){
            return $query->where('homeworks.is_active', true);
        }
        if($withHomeworkActive==='is_not_active'){
            return $query->where('homeworks.is_active', false);
        }
        return $query;
    }
    public function scopeWithOwner($query, $withOwner=0)
    {
        if($withOwner>0 && is_numeric($withOwner)){
            return $query->where('homework_registrations.user_id', $withOwner);
        }
        return $query;
    }
    public function assignRequiredCritique($thread_id=0)
    {
        $this->update(['required_critique_thread_id' => $thread_id]);
    }
}
