<?php

namespace App\Listeners;

use App\Events\NewPost;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;
use App\Models\Activity;
use Cache;
use App\Sosadfun\Traits\ThreadObjectTraits;
use Log;
use App\Jobs\SendUpdateNotification;

// class NewPostListener implements ShouldQueue
class NewPostListener
{

    use ThreadObjectTraits;

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
    * @param  NewPost  $event
    * @return void
    */
    public function handle(NewPost $event)
    {
        $post = $event->post;
        $thread = $post->thread;

        DB::transaction(function() use($thread, $post){
            // 更新原楼里的信息更改
            if($post->type!='comment'){
                $thread->last_post_id = $post->id;
                $thread->responded_at = $post->created_at;
            }

            if($post->type!='post'&&$post->type!='comment'){
                $thread->last_component_id = $post->id;
                if($thread->first_component_id===0){
                    $thread->first_component_id = $post->id;
                }
                if($post->post_check('standard_chapter')){
                    $thread->add_component_at = $post->created_at;
                }
                $thread->recalculate_characters();

                if($post->type==='chapter'){
                    $thread->reorder_components();
                }
                $thread->check_bianyuan();

                $this->clearThread($thread->id);
            }else{
                $thread->reply_count+=1;
            }

            $thread->save();

            // 更新被回复对象
            if($post->parent){
                $post->parent->update([
                    'reply_count'=> $post->parent->reply_count+1,
                    'view_count'=> $post->parent->view_count+1,
                    'responded_at' => $post->created_at,
                    'last_reply_id' => $post->id,
                ]);
                // TODO reply position count increment
            }

            // 如果这是一篇批评，更新critique count
            if($post->type==='critique'){
                $thread->increment_new_critique($post);
            }

            //回帖了，不是给自己的帖子回帖，那么送出回帖提醒
            if(($post->reply_to_id>0)&&$post->parent&&($post->user_id!=$post->parent->user_id)){
                $reply_activity = Activity::create([
                    'kind' => 1,
                    'item_type' => 'post',
                    'item_id' => $post->id,
                    'user_id' => $post->parent->user_id,
                ]);
                $post->parent->user->remind('new_reply');
            }

            //不是给自己的主题回帖，这个贴也不是点评，没有回复对象或者回复的对象不是楼主，那么给楼主送出跟帖提醒
            if($post->user_id!=$thread->user_id&&$post->type!='comment'&&(!$post->parent||$post->parent->user_id!=$thread->user_id)){
                $post_activity = Activity::create([
                    'kind' => 1,
                    'item_type' => 'post',
                    'item_id' => $post->id,
                    'user_id' => $thread->user_id,
                ]);
                $thread->user->remind('new_reply');
            }

            // 修改惯用马甲，惯用indentation
            // $post->user->created_new_post($post);
        });

        //如果书籍章节更新，或者非书籍回复，那么告诉不是自己的，所有收藏本讨论串、愿意接受更新的读者, 这里发生了更新
        if(($post->type==='chapter'&&$thread->channel()->type==='book')||($thread->channel()->type!='book')){
            SendUpdateNotification::dispatch('thread_has_new_component', $thread->id, $post->user_id);
        }
    }
}
