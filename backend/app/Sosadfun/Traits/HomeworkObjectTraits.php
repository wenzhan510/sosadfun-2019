<?php
namespace App\Sosadfun\Traits;

use DB;
use Cache;
use ConstantObjects;

trait HomeworkObjectTraits{

    public function findActiveHomeworks()
    {
        return Cache::remember('ActiveHomeworks', 15, function (){
            return \App\Models\Homework::where('is_active',true)->with('registration_thread',  'summary_thread','registrations.owner')->orderBy('id','desc')->get();
        });
    }

    public function findFinishedHomeworks()
    {
        return Cache::remember('FinishedHomeworks', 15, function (){
            return \App\Models\Homework::where('is_active',false)->with('registration_thread', 'summary_thread','registrations.owner')->orderBy('id','desc')->get();
        });
    }

    public function refreshActiveHomeworks()
    {
        Cache::forget('ActiveHomeworks');
    }

    public function refreshFinishedHomeworks()
    {
        Cache::forget('FinishedHomeworks');
    }

    public function findHomeworkProfile($id)
    {
        return Cache::remember('HomeworkProfile.'.$id, 15, function () use ($id){
            $homework = \App\Models\Homework::find($id);
            if($homework){
                $homework->load('registration_thread', 'summary_thread','registrations.owner','registrations.briefThread.briefPosts.author');
            }
            return $homework;
        });
    }

    public function findHomework($id)
    {
        return Cache::remember('Homework.'.$id, 15, function () use ($id){
            $homework = \App\Models\Homework::find($id);
            return $homework;
        });
    }

    public function refreshHomework($id)
    {
        Cache::forget('Homework.'.$id);
        Cache::forget('HomeworkProfile.'.$id);
    }

}
