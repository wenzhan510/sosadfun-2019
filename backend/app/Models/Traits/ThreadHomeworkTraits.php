<?php
namespace App\Models\Traits;

use Cache;

trait ThreadHomeworkTraits{

    public function find_homework_registration_via_thread()//
    {
        $thread = $this;
        if(!$thread||$thread->channel()->type!='homework'){return null;}
        return Cache::remember('findHomeworkRegistrationViaThread.'.$thread->id, 10, function () use($thread){
            $homework_registration = $thread->homework_registration;
            $homework_registration->load('homework');
            return $homework_registration;
        });
    }
}
