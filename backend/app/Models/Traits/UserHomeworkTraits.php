<?php
namespace App\Models\Traits;

use Cache;
use Carbon;

trait UserHomeworkTraits{

    public function purchased_homeworks()//
    {
        $user = $this;
        if(!$user){return false;}
        return Cache::remember('UserPurchasedHomeworks'.$user->id, 10, function () use($user){
            return \App\Models\HomeworkPurchase::where('user_id',$user->id)->get();
        });
    }

    public function refreshPurchasedHomeworks()//
    {
        Cache::forget('UserPurchasedHomeworks'.$this->id);
    }

    public function purchasedThisHomework($homework_id)//
    {
        return $this->purchased_homeworks()->where('homework_id', $homework_id)->count()>0;
    }

    public function active_registrations()//
    {
        $user = $this;
        if(!$user){return false;}
        return Cache::remember('userActiveRegistrations'.$user->id, 10, function () use($user){
            return \App\Models\HomeworkRegistration::with('briefThread.briefPosts.author','required_critique_thread','homework')
            ->join('homeworks', 'homeworks.id', '=', 'homework_registrations.homework_id')
            ->withOwner($user->id)
            ->homeworkActive('is_active')
            ->select('homework_registrations.*','homeworks.level as homework_level')
            ->get();
        });
    }
    public function refreshAciveRegistrations()//
    {
        Cache::forget('userActiveRegistrations'.$this->id);
    }


    public function past_registrations()//
    {
        $user = $this;
        if(!$user){return false;}
        return Cache::remember('userPastRegistrations'.$user->id, 10, function () use($user){
            return \App\Models\HomeworkRegistration::with('briefThread.briefPosts.author','required_critique_thread','homework')
            ->join('homeworks', 'homeworks.id', '=', 'homework_registrations.homework_id')
            ->withOwner($user->id)
            ->homeworkActive('is_not_active')
            ->select('homework_registrations.*','homeworks.level as homework_level')
            ->get();
        });
    }

    public function participatingHomeworksWithLevelBiggerThan($level)//
    {
        return $this->active_registrations()->where('homework_level', '>=', $level)->count()>0;
    }

    public function participatingThisActiveHomework($homework_id)//
    {
        return $this->active_registrations()->where('homework_id', $homework_id)->count()>0;
    }

    public function active_homework_invitations()//
    {
        $user = $this;
        if(!$user){return false;}
        return Cache::remember('userActiveHomeworkInvitations'.$user->id, 10, function () use($user){
            return \App\Models\HomeworkInvitation::with('owner','homework')
            ->where('is_redeemed', false)
            ->where('user_id', $user->id)
            ->where('valid_until', '>', Carbon::now())
            ->get();
        });
    }

    public function homework_invitations()//
    {
        $user = $this;
        if(!$user){return false;}
        return Cache::remember('userHomeworkInvitations'.$user->id, 10, function () use($user){
            return \App\Models\HomeworkInvitation::with('owner','homework')
            ->where('user_id', $user->id)
            ->get();
        });
    }

    public function hasHomeworkInvitationFor($homework_id, $level, $role)//
    {
        return $this->active_homework_invitations()
        ->whereIn('role', [$role, null])
        ->where('level', '>=', $level)
        ->whereIn('homework_id', [$homework_id, 0])
        ->count()>0;
    }

    public function useHomeworkInvitationFor($homework_id, $level, $role)//
    {
        $invitation = $this->active_homework_invitations()
        ->whereIn('role', [$role, null])
        ->where('level', '>=', $level)
        ->whereIn('homework_id', [$homework_id, 0])
        ->first();

        if(!$invitation){return false;}

        return $invitation->update(['is_redeemed' => true]);
    }

    public function createHomeworkInvitation($homework_id, $level, $role, $days)//
    {
        if(!in_array($role, ['worker', 'critic', 'watcher', 'reader'])){
            return false;
        }
        $invitation = \App\Models\HomeworkInvitation::create([
            'token' => str_random(40),
            'level' => $level,
            'role' => $role,
            'homework_id' => $homework_id,
            'user_id' => $this->id,
            'valid_until' => Carbon::now()->addDays($days),
        ]);
        return $invitation;

    }

    public function no_homework($days=0)// 将用户禁止使用作业区的时间增加到这个天数
    {
        $this->no_homework = 1;
        $info = $this->info;
        $info->no_homework_until = $info->no_homework_until>Carbon::now() ? $info->no_homework_until->addDays($days) : Carbon::now()->addDays($days);
        $this->save();
        $info->save();
    }

}
