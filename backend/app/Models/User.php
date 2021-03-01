<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use App\Notifications\ResetPasswordNotification;
use DB;
use Carbon;
use Cache;
use CacheUser;
use ConstantObjects;
use App\Sosadfun\Traits\FindThreadTrait;

class User extends Authenticatable
{
    use SoftDeletes;
    use Notifiable;
    use HasApiTokens;
    use FindThreadTrait;
    use Traits\UserHomeworkTraits;

    protected $connection= 'mysql::write';

    protected $dates = ['deleted_at', 'created_at'];
    const UPDATED_AT = null;

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'name', 'email', 'password', 'title_id', 'level', 'quiz_level', 'no_ads', 'no_homework', 'lang', 'activated'
    ];

    /**
    * The attributes that should be hidden for arrays.
    *
    * @var array
    */
    protected $hidden = [
        'password', 'email', 'remember_token',
    ];

    public static function boot()
    {
        parent::boot();
    }

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    public function lists()
    {
        return $this->hasMany(Thread::class)->whereIn('channel_id', ConstantObjects::publicChannelTypes('list'));
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function checkins()
    {
        return $this->hasMany(Checkin::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function title()
    {
        return $this->belongsTo(Title::class, 'title_id');
    }

    public function titles()
    {
        return $this->belongsToMany(Title::class, 'title_user', 'user_id', 'title_id')->withPivot('is_public');
    }

    public function public_titles()
    {
        return $this->belongsToMany(Title::class, 'title_user', 'user_id', 'title_id')->wherePivot('is_public',1)->withPivot('is_public');
    }

    public function branchaccounts()
    {
        return $this->belongsToMany(User::class, 'linkaccounts', 'master_account', 'branch_account');
    }

    public function masteraccounts()
    {
        return $this->belongsToMany(User::class, 'linkaccounts', 'branch_account', 'master_account');
    }

    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    public function info()
    {
        return $this->hasOne(UserInfo::class, 'user_id');
    }

    public function password_reset()
    {
        return $this->hasOne(PasswordReset::class, 'email', 'email');
    }

    public function intro()
    {
        return $this->hasOne(UserIntro::class, 'user_id');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    public function followings()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }

    public function isFollowing($id)
    {
        return \App\Models\Follower::where('user_id', $id)->where('follower_id', $this->id)->count();
    }

    public function homeworks()
    {
        return $this->belongsToMany(Homework::class, 'homework_registrations', 'user_id', 'homework_id');
    }

    public function collections()
    {
        return $this->belongsToMany(Thread::class, 'collections', 'user_id', 'thread_id');
    }

    public function collection_groups()
    {
        return $this->hasMany(CollectionGroup::class, 'user_id');
    }

    public function isCollecting($thread_id)
    {
        return Collection::where('user_id',$this->id)->where('thread_id', $thread_id)->count();
    }

    public function emailmodifications()
    {
        return $this->hasMany(HistoricalEmailModification::class, 'user_id');
    }

    public function passwordresets()
    {
        return $this->hasMany(HistoricalPasswordReset::class, 'user_id');
    }

    public function donations()
    {
        return $this->hasMany(DonationRecord::class, 'user_id');
    }

    public function registrationapplications()
    {
        return $this->hasMany(RegistrationApplication::class, 'user_id');
    }

    public function usersessions()
    {
        return $this->hasMany(HistoricalUserSession::class, 'user_id');
    }

    public function invitation_tokens()
    {
        return $this->hasMany(InvitationToken::class, 'user_id');
    }

    public function reward_tokens()
    {
        return $this->hasMany(RewardToken::class, 'user_id');
    }

    public function patreon()
    {
        return $this->hasOne(Patreon::class, 'user_id');
    }

    public function donation_records()
    {
        return $this->hasMany(DonationRecord::class, 'user_id');
    }

    public function follow($user_ids)
    {
        if (!is_array($user_ids)){
            $user_ids = compact('user_ids');
        }
        $this->followings()->sync($user_ids, false);
    }
    public function unfollow($user_ids)
    {
        if (!is_array($user_ids)){
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }

    public function hasTitle($id)
    {
        if($id==0){return true;}
        $title = ConstantObjects::title_type('level')->keyby('id')->get($id);
        return ($title&&$title->level<=$this->level)||($this->titles->contains($id));
    }

    public function isAdmin()
    {
        return $this->role==='admin';
    }

    public function isEditor()
    {
        return $this->role==='editor';
    }

    public function isSenior()
    {
        return $this->role==='senior';
    }

    public function isReviewer() // 审核员
    {
        return $this->role==='reviewer';
    }

    public function canReview()
    {
        return $this->isAdmin()||$this->isReviewer()||$this->isEditor();
    }

    public function canSeeChannel($id)
    {
        $channel = collect(config('channel'))->keyby('id')->get($id);
        return $channel->is_public||$this->isAdmin()||($channel->type==='homework'&&$this->isEditor())||($channel->type==='homework'&&$this->isSenior());
    }

    public function canSeePost($post)
    {
        if($post->user_id === $this->id){
            return true;
        }
        $thread = $this->findThread($post->thread_id);
        if($this->canSeeThread($thread)){
            return true;
        }
    }

    public function canSeeThread($thread)
    {
        if($thread->user_id===$this->id||$this->isAdmin()){
            return true;
        }
        if($thread->is_public&&$this->canSeeChannel($thread->channel_id)){
            return true;
        }
        if($thread->is_public&&$thread->channel()->type==='homework'&&$thread->find_homework_registration_via_thread()&&$this->canSeeHomework($thread->find_homework_registration_via_thread()->homework)){
            return true;
        }
    }

    public function canCommentThread($thread)
    {
        if($this->isAdmin()){return true;}
        if(($thread->is_locked)||($thread->no_reply&&$thread->user_id!=$this->id)){return false;}
        if($this->no_posting){return false;}
        if($thread->is_public&&$thread->channel()->type==='homework'&&$thread->find_homework_registration_via_thread()&&$this->canSeeHomework($thread->find_homework_registration_via_thread()->homework)){
            return true;
        }
        return true;
    }

    public function canSeeAnyHomework()
    {
        if($this->isAdmin()||$this->isEditor()||$this->isSenior()){
            return true;
        }
    }

    public function canSeeHomework($homework)
    {
        if($this->canSeeAnyHomework()){
            return true;
        }

        if($this->purchasedThisHomework($homework->id)){ //如果用户购买了本次作业，可以看
            return true;
        }

        if($homework->is_active && $this->participatingHomeworksWithLevelBiggerThan($homework->level)){ //如果是未结束的作业，参加、评论、围观了本次作业，可以看
            return true;
        }
        return false;
    }

    public function canCommentHomework($homework)
    {
        if($this->canSeeAnyHomework()){
            return true;
        }
        if($homework->is_active && $this->participatingHomeworksWithLevelBiggerThan($homework->level)){ //如果是未结束的作业，参加、评论了本次作业，或参加、评论了比本次作业等级更高的作业，可以评论
            return true;
        }
        return false;
    }

    public function checklevelup()
    {
        $level_ups = config('level.level_up');
        $info = $this->info;
        foreach($level_ups as $level=>$requirement){
            if (($this->level < $level)
            &&(!(array_key_exists('salt',$requirement))||($requirement['salt']<=$info->salt))
            &&(!(array_key_exists('fish',$requirement))||($requirement['fish']<=$info->fish))
            &&(!(array_key_exists('ham',$requirement))||($requirement['ham']<=$info->ham))
            &&(!(array_key_exists('qiandao_all',$requirement))||($requirement['qiandao_all']<=$info->qiandao_all))
            &&(!(array_key_exists('quiz_level',$requirement))||($requirement['quiz_level']<=$this->quiz_level))){
                $this->level = $level;
                $this->save();
                return true;
            }
        }
        return false;
    }

    public function reward($kind, $base = 0){
        return $this->info->reward($kind, $base);
    }

    public function retract($kind, $base = 0){
        return $this->info->retract($kind, $base);
    }

    public function isOnline()
    {
        return Cache::has('usr-on-'.$this->id);
    }

    public function wearTitle($title_id)
    {
        $this->update([
            'title_id' => $title_id,
        ]);
    }

    public function sendPasswordResetNotification($token)
    {
        Cache::put($token, $this->email, 60);
        Cache::put($this->email,$token, 60);
        $this->notify(new ResetPasswordNotification($token));
    }
    public function active_now($ip)
    {
        $this->info->active_now($ip);
    }

    public function linked($user_id)
    {
        return $this->branchaccounts->contains($user_id);
    }

    public function remind($reminder='')
    {
        $info = CacheUser::info($this->id);
        switch ($reminder) {
            case 'new_message':
            if(!$info->no_message_reminders){
                $info->unread_reminders +=1;
                $info->message_reminders += 1;
                $info->save();
            }
            break;

            case 'new_reply':
            if(!$info->no_reply_reminders){
                $info->unread_reminders +=1;
                $info->reply_reminders +=1;
                $info->save();
            }
            break;

            case 'new_reward':
            if(!$info->no_reward_reminders){
                $info->unread_reminders +=1;
                $info->reward_reminders +=1;
                $info->save();
            }
            break;

            case 'new_upvote':
            if(!$info->no_upvote_reminders){
                $info->unread_reminders +=1;
                $info->upvote_reminders +=1;
                $info->save();
            }
            break;

            case 'new_administration':
                $info->unread_reminders +=1;
                $info->administration_reminders +=1;
                $info->save();
            break;

            default:
            return false;
        }

        return true;
    }

    public function created_new_post($post)
    {
        if(!$post){return;}

        if($this->use_indentation!=$this->use_indentation){
            $this->use_indentation=$this->use_indentation;
        }
        if($post->is_anonymous&& $this->majia!=$post->majia){
            $this->majia=$post->majia;
        }

        $this->save();
    }

    public function scopeNameLike($query, $name)
    {
        if($name){
            return $query->where('name','like','%'.$name.'%');
        }
        return $query;
    }

    public function scopeEmailLike($query, $email)
    {
        if($email){
            return $query->where('email','like','%'.$email.'%');
        }
        return $query;
    }

    public function scopeCreationIPLike($query, $ip)
    {
        if($ip){
            return $query->join('user_infos','user_infos.user_id','=','users.id')
            ->where('user_infos.creation_ip','like', $ip.'%')
            ->select('users.*');
        }
        return $query;
    }

    public function scopeNameOrEmailLike($query, $name)
    {
        if($name){
            return
            $query->where('name','like','%'.$name.'%')->orWhere('email','like','%'.$name.'%');
        }
        return $query;
    }

    public function cancel_donation_reward()
    {
        $info = $this->info;
        $info->donation_level = 0;
        $info->save();
    }

    public function donation_level_by_amount($amount=0)
    {
        $level = 0;
        foreach(config('donation') as $key=>$donation)
        {
            if($key>$level&&$donation['amount']<=$amount){
                $level = $key;
            }
        }
        return $level;
    }


    public function admin_reset_password()
    {
        \App\Models\HistoricalPasswordReset::create([
            'user_id' => $this->id,
            'ip_address' => request()->ip(),
            'old_password' => $this->password,
            'admin_reset' => 1,
        ]);
        $this->forceFill([
            'password' => bcrypt(str_random(10)),
            'remember_token' => str_random(60),
            'activated' => 0,
            'no_logging' => 1,
        ])
        ->save();
    }

    public function no_log($days=0)// 将用户禁止登陆增加到这个天数
    {
        $this->no_logging = 1;
        $info = $this->info;
        $info->no_logging_until = $info->no_logging_until>Carbon::now() ? $info->no_logging_until->addDays($days) : Carbon::now()->addDays($days);
        $this->save();
        $info->save();
    }

    public function allow_log($days=0)// 解禁用户
    {
        $this->no_logging = 0;
        $info = $this->info;
        $info->no_logging_until = null;
        $this->save();
        $info->save();
    }

    public function check_default_collection_group($collection_group_id, $doSet=true)
    {
        if($doSet&&$this->info->default_collection_group_id!=$collection_group_id){
            $this->info->update([
                'default_collection_group_id' => $collection_group_id,
            ]);
        }

        if(!$doSet&&$this->info->default_collection_group_id===$collection_group_id){
            $this->info->update([
                'default_collection_group_id' => 0,
            ]);
        }
    }

}
