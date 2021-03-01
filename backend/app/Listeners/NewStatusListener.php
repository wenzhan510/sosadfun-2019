<?php

namespace App\Listeners;

use App\Events\NewStatus;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;
use App\Models\Activity;
use Cache;

//class NewStatusListener implements ShouldQueue
class NewStatusListener
{
    public $tries = 3;

    /**
    * Create the event listener.
    *
    * @return void
    */
    public function __construct()
    {
        //
    }

    /**
    * Handle the event.
    *
    * @param  NewStatus  $event
    * @return void
    */
    public function handle(NewStatus $event)
    {
        $status = $event->status;

        DB::transaction(function() use($status){
            // 更新被回复对象
            if($status->parent){
                $status->parent->update([
                    'reply_count'=> $status->parent->reply_count+1,
                    'last_reply_id' => $status->id,
                ]);
            }
            // 转发动态的情况，递增动态转发数量
            if($status->attachable_type==='status'&&$status->attachable){
                $status->attachable->update([
                    'forward_count'=> $status->attachable->forward_count+1,
                ]);
            }

            //回帖了，不是给自己的动态回帖，那么送出回帖提醒
            if(($status->reply_to_id>0)&&$status->parent&&($status->user_id!=$status->parent->user_id)){
                $reply_activity = Activity::create([
                    'kind' => 1,
                    'item_type' => 'status',
                    'item_id' => $status->id,
                    'user_id' => $status->parent->user_id,
                ]);
                $status->parent->user->remind('new_reply');
            }
        });

    }
}
