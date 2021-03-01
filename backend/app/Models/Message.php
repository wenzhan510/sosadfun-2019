<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon;

class Message extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    protected $dates = ['deleted_at','created_at'];
    const UPDATED_AT = null;

    public function message_body()
    {
        return $this->belongsTo(MessageBody::class, 'body_id');
    }

    public function poster()
    {
        return $this->belongsTo(User::class, 'poster_id')->select('id','name');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id')->select('id','name');
    }

    public function scopeWithUser($query, $id=0)
    {
        return $query->where('poster_id',$id)
        ->orWhere('receiver_id',$id);
    }

    public function scopeWithPoster($query, $id=0)
    {
        return $query->where('poster_id',$id);
    }

    public function scopeWithReceiver($query, $id=0)
    {
        return $query->where('receiver_id',$id);
    }

    public function scopeWithDialogue($query, $user1=0, $user2=0)
    {
        return $query->where([
            ['poster_id', '=', $user1],
            ['receiver_id', '=', $user2],
        ])
        ->orWhere([
            ['poster_id', '=', $user2],
            ['receiver_id', '=', $user1],
        ]);
    }

    public function scopeWithInDays($query, $days = 2)
    {
        return $query->where('created_at','<',Carbon::now()->subDays($days));
    }

    public function scopeWithRead($query, $readstatus)
    {
        if($readstatus === 'read_only') {
            return $query->where('seen', 1);
        }else if($readstatus === 'unread_only') {
            return $query->where('seen', 0);
        }
    }

    public function scopeOrdered($query, $ordered = '')
    {
        switch ($ordered) {
            case 'oldest'://最老的
            return $query->orderBy('created_at', 'asc');
            break;

            default://默认按时间顺序排列，显示最晚的
            return $query->orderBy('created_at', 'desc');
        }
    }

}
