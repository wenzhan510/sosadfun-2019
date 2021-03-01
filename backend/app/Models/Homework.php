<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon;

class Homework extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $dates = ['registration_start_at', 'submission_end_at', 'critique_end_at', 'created_at', 'end_at', 'deleted_at'];
    const UPDATED_AT = null;

    public function registration_thread()
    {
        return $this->belongsTo(Thread::class, 'registration_thread_id')->brief();
    }

    public function profile_thread()
    {
        return $this->belongsTo(Thread::class, 'profile_thread_id')->brief();
    }

    public function summary_thread()
    {
        return $this->belongsTo(Thread::class, 'summary_thread_id')->brief();
    }

    public function registrations()
    {
        return $this->hasMany(HomeworkRegistration::class, 'homework_id')->orderBy('order_id','asc');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'homework_registrations', 'homework_id', 'user_id');
    }

    public function purchases()
    {
        return $this->hasMany(HomeworkPurchase::class, 'homework_id');
    }

    public function buyers()
    {
        return $this->hasMany(User::class, 'homework_purchases', 'homework_id', 'user_id')->select('id','name');
    }


    public function registed_users()
    {
        return $this->belongsToMany(User::class, 'homework_registrations', 'homework_id', 'user_id')->select('users.id','name')->withPivot('majia');
    }

    public function thread()
    {
        if($this->summary_thread){
            return $this->summary_thread;
        }
        if($this->registration_thread){
            return $this->registration_thread;
        }
        return null;
    }

    public function registration_count($role='')
    {
        if($role){
            return $this->registrations->where('role',$role)->count();
        }
        return $this->registrations->count();
    }

    public function thread_count($status='')
    {
        if($status==='finished_only'){
            return $this->registrations->where('finished_at', '<>', null)->count();
        }
        if($status==='none_finished_only'){
            return $this->registrations->where('finished_at', '=', null)->count();
        }
        return $this->registrations->where('thread_id', '>', 0)->count();
    }

    public function registedByUser($user, $role='', $majia=''){
        if(!in_array($role, ['worker','critic'])){return false;}
        $registration = HomeworkRegistration::firstOrCreate([
            'homework_id' => $this->id,
            'user_id' => $user->id,
        ],[
            'role' => $role,
            'registered_at' => Carbon::now(),
            'majia' => $majia,
        ]);
        $this->decrement($role.'_registration_limit');
        return $registration;
    }

    public function purchasedByUser($user){
        $purchase = HomeworkPurchase::firstOrCreate([
                'homework_id' => $this->id,
                'user_id' => $user->id,
        ]);
        $this->increment('purchase_count');
        return $purchase;
    }

    public function massAssignRequiredCritique($thread_id)
    {
        $all_registrations = $this->registrations;

        $worker_registrations = $all_registrations->where('thread_id','<>',$thread_id)->where('required_critique_thread_id',0)->where('role','worker');

        if(!$worker_registrations->isEmpty()){
            $registrations = $worker_registrations->random(1);
            foreach($registrations as $registration){
                $registration->assignRequiredCritique($thread_id);
            }
        }

        $critic_registrations = $all_registrations->where('thread_id','<>',$thread_id)->where('required_critique_thread_id',0)->where('role','critic');

        if(!$critic_registrations->isEmpty()){
            $registrations = $critic_registrations->random(1);
            foreach($registrations as $registration){
                $registration->assignRequiredCritique($thread_id);
            }
        }
    }

    public function assign_order_id()
    {
        $max_order = 0;
        $registration = $this->registrations->sortByDesc('order_id')->first();
        if($registration){$max_order = $registration->order_id;}
        return $max_order+1;
    }
}
