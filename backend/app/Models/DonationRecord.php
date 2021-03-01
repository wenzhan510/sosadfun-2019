<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ConstantObjects;
use Carbon;
use DB;

class DonationRecord extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $dates = ['donated_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id','name','title_id','level');
    }

    public function scopeEmailLike($query, $email)
    {
        if($email){
            return $query->where('donation_email','like','%'.$email.'%');
        }
        return $query;
    }

    public function reward_user()
    {
        if($this->is_claimed){return false;}

        if($this->donated_at<Carbon::now()->subMonth()){return false;}

        $user = $this->user;
        if(!$user){return false;}

        $info = $user->info;

        $donation_level = $user->donation_level_by_amount($this->donation_amount);

        if($donation_level>1){
            $user->no_ads = true;
        }

        $info->donation_level = $donation_level;

        $titles = ConstantObjects::title_type('patreon')->where('level','<=',$donation_level)->pluck('id')->toArray();
        $user->titles()->syncWithoutDetaching($titles);

        if($donation_level===3){
            $info->qiandao_reward_limit += 1;
        }

        if($donation_level===4){
            $info->no_ads_reward_limit = 5;
            $info->qiandao_reward_limit += 5;
        }
        if($donation_level===5){
            $info->no_ads_reward_limit = 20;
            $info->qiandao_reward_limit += 20;
        }

        $user->save();
        $info->save();
        $this->is_claimed = 1;
        $this->save();
        return true;
    }
}
