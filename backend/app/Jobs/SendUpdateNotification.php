<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use DB;
use Log;
use Carbon;

class SendUpdateNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $item_type, $item_id, $author_id;

    public $tries = 3;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($item_type, $item_id, $author_id)
    {
        $this->item_type = $item_type;
        $this->item_id = $item_id;
        $this->author_id = $author_id;
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags()
    {
        return ['single', $this->item_type.':'.$this->item_id];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $item_type = $this->item_type;
        $item_id = $this->item_id;
        $author_id = $this->author_id;

        if($item_type==='thread_has_new_component'){
            // DB::table('collections')
            // ->join('user_infos','user_infos.user_id','=','collections.user_id')
            // ->join('users','users.id','=','collections.user_id')
            // ->where([['collections.thread_id','=',$item_id],['collections.keep_updated','=',1],['collections.user_id','<>',$author_id],['collections.group_id','=',0]])
            // ->update([
            //     'collections.updated'=>1,
            //     'user_infos.default_collection_updates'=>DB::raw('user_infos.default_collection_updates + 1'),
            //     'user_infos.unread_updates' => DB::raw('user_infos.unread_updates + 1'),
            // ]);
            //
            // DB::table('collections')
            // ->join('user_infos','user_infos.user_id','=','collections.user_id')
            // ->join('users','users.id','=','collections.user_id')
            // ->join('collection_groups','collection_groups.id','=','collections.group_id')
            // ->where([['collections.thread_id','=',$item_id],['collections.keep_updated','=',1],['collections.user_id','<>',$author_id]])
            // ->update([
            //     'collections.updated'=>1,
            //     'collection_groups.update_count'=>DB::raw('collection_groups.update_count + 1'),
            //     'user_infos.unread_updates' => DB::raw('user_infos.unread_updates + 1'),
            // ]);
        }

    }
}
