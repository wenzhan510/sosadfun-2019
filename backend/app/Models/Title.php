<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use CacheUser;

class Title extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    public function owners()
    {
        return $this->belongsToMany(User::Class, 'title_user', 'title_id', 'user_id');
    }
    public function check_availability($user_id=0)
    {
        if($this->type==='task'){
            if($this->id===config('constants.task_titles.2019winter')){
                $user = CacheUser::user($user_id);
                if($user&&$user->created_at < "2020-01-01 08:00:00"){
                    return true;
                }
            }
        }
        return false;
    }
}
