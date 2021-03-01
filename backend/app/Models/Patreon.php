<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Patreon extends Model
{
    const UPDATED_AT = null;
    protected $guarded = [];
    protected $dates = ['created_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id','name','title_id','level');
    }

    public function donation_records()
    {
        return $this->hasMany(DonationRecord::class, 'donation_email', 'patreon_email');
    }

    public function sync_records()
    {
        DB::table('donation_records')->where('donation_email',$this->patreon_email)->where('user_id',0)->update(['user_id'=>$this->user_id]);

        $records = DB::table('donation_records')->where('donation_email',$this->patreon_email)->count();

        if($records>0){
            $this->update(['is_approved'=>1]);
        }
    }
}
