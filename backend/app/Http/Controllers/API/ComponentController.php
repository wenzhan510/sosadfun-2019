<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Thread;
use App\Models\Post;
use App\Models\PostInfo;
use CacheUser;
use Auth;
use Carbon;
use StringProcess;
use App\Sosadfun\Traits\ThreadObjectTraits;
use App\Sosadfun\Traits\PostObjectTraits;


class ComponentController extends Controller
{
    use ThreadObjectTraits;
    use PostObjectTraits;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function update_component_index($id, Request $request)
    {
        $thread = Thread::find($id);
        $user = auth('api')->user();
        if(!$thread){abort(404);}
        if($thread->user_id!=$user->id||($thread->is_locked&&!$user->isAdmin())){abort(403);}

        $posts = $thread->component_index();

        foreach($request->order_by as $key=>$order_by){
            if(is_numeric($order_by)){
                $post = $posts->firstWhere('id', $key);
                $post->info->update(['order_by' => $order_by]);
            }
        }
        $thread->reorder_components();

        $first = $request->first_component_id;
        if($first&&is_numeric($first)){
            $post = $posts->firstWhere('id', $first);
            if($post&&$post->user_id===$user->id&&$post->type==='chapter'){
                $thread->update(['first_component_id'=>$first]);
            }
        }
        $last = $request->last_component_id;
        if($last&&is_numeric($last)){
            $post = $posts->firstWhere('id', $last);
            if($post&&$post->user_id===$user->id&&$post->type==='chapter'){
                $thread->update(['last_component_id'=>$last]);
            }
        }

        $this->clearThread($id);
        $thread = $this->threadProfile($id);

        return response()->success([
            'thread' => new ThreadProfileResource($thread),
        ]);
    }

    public function convert($id, Request $request){

        // $request->convert_to_type;
        $post = Post::find($id);
        if(!$post){abort(404);} // post必须存在
        $thread=$post->thread;
        if(!$thread){abort(404);}  // thread必须存在
        if($thread->is_locked||$thread->user_id!=auth('api')->id()||auth('api')->user()->no_posting){abort(403);} // 本人，且未禁言，且未锁定

        $this->validate($request, [
            'convert_to_type' => 'required|string|max:10',
        ]);

        if($request->convert_to_type==='post'&&$post->type!="post"){
            $post_info = $post->info;
            if($post_info){
                $post_info->delete();
            }
            $post->update([
                'type' => 'post',
                'edited_at' => Carbon::now(),
            ]);
        }

        if($request->convert_to_type!='post'&&$post->type==="post"){
            if(($thread->channel()->type==='book'&&in_array($request->convert_to_type, ['chapter','volumn']))||($thread->channel()->type==='list'&&in_array($request->convert_to_type, ['review']))||($thread->channel()->type==='column'&&in_array($request->convert_to_type, ['essay']))){
                $previous_chapter = $thread->last_component;
                $order_by = ($previous_chapter&&$previous_chapter->info)? ($previous_chapter->info->order_by+1):1;
                PostInfo::updateOrCreate([
                    'post_id' => $post->id,
                ],[
                    'order_by' => $order_by,
                    'abstract'=>StringProcess::trimtext($post->body,150),
                ]);
                $post->update([
                    'type' => $request->convert_to_type,
                    'reply_to_id' => 0,
                    'reply_to_brief' => '',
                    'in_component_id' => 0,
                    'edited_at' => Carbon::now(),
                ]);
            }
            if($thread->channel()->type==='box'&&in_array($request->convert_to_type,['answer'])){
                PostInfo::updateOrCreate([
                    'post_id' => $post->id,
                ],[
                    'order_by' => 1,
                    'abstract'=>StringProcess::trimtext($post->body,150),
                ]);
                $post->update([
                    'type' => $request->convert_to_type,
                    'edited_at' => Carbon::now(),
                ]);
            }
            if($thread->channel()->type==='box'&&in_array($request->convert_to_type,['question'])){
                if($post_info){
                    $post_info->delete();
                }
                $post->update([
                    'type' => $request->convert_to_type,
                    'edited_at' => Carbon::now(),
                ]);
            }
            if($thread->channel()->type==='homework'&&in_array($request->convert_to_type,['work','critique'])){
                if($post_info){
                    $post_info->delete();
                }
                $post->update([
                    'type' => $request->convert_to_type,
                    'reply_to_id' => 0,
                    'reply_to_brief' => '',
                    'in_component_id' => 0,
                    'edited_at' => Carbon::now(),
                ]);
            }
        }

        $this->clearPost($post->id);
        $this->clearThread($thread->id);

        return response()->success(new PostResource($post));
    }

}
