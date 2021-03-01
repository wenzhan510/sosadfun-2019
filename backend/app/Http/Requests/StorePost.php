<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Thread;
use App\Models\Post;
use App\Models\PostInfo;
use DB;
use App\Sosadfun\Traits\GeneratePostDataTraits;

class StorePost extends FormRequest
{
    use GeneratePostDataTraits;
    /**
    * Determine if the user is authorized to make this request.
    *
    * @return bool
    */
    public function authorize()
    {
        return true;
    }

    /**
    * Get the validation rules that apply to the request.
    *
    * @return array
    */
    public function rules()
    {
        return [
            'body' => 'required|string|min:10|max:20000',
            'reply_to_id' => 'numeric',
            'reply_to_brief' => 'string|nullable|max:30',
            'reply_to_position' => 'numeric',
            'majia' => 'string|nullable|max:10',
            'title' => 'string|nullable|max:30',
            'brief' => 'string|nullable|max:50',
            'type' => 'string|nullable|max:10',
            'rating' => 'numeric|nullable|min:0|max:10',
            'reviewee_id' => 'numeric|nullable|min:0',
            'annotation' => 'string|nullable|max:2000',
            'warning' => 'string|nullable|max:2000',
        ];
    }

    public function storePost($thread)
    {
        if(!$thread){abort(404);} // 不存在的内容不能修改
        if($thread->no_reply&&$thread->user_id!=auth('api')->id()&&!auth('api')->user()->isAdmin()){abort(403);} // 禁止回复状态下，非楼主，非管理员不能跟帖
        if(!$thread->is_public&&$thread->user_id!=auth('api')->id()&&!auth('api')->user()->isAdmin()){abort(403);} // 未公开状态下，非楼主，非管理员不能跟帖
        if($thread->is_locked&&!auth('api')->user()->isAdmin()){abort(403);} // 锁定状态下，非管理员不能跟帖

        $post_data = $this->generatePostData($thread);
        $post_data = $this->addReplyData($post_data, $thread);
        $info_data = $this->generatePostInfoData($post_data, $thread);
        $post_data = $this->validateBianyuan($post_data, $thread);
        $post = DB::transaction(function()use($post_data, $info_data){
            $post = Post::create($post_data);
            if($info_data){
                $info_data['post_id']=$post->id;
                $info = PostInfo::create($info_data);
            }
            return $post;
        });
        return $post;
    }

    public function updatePost($post)
    {
        if(!$post){abort(404);}// 不存在的内容不能修改
        $info = $post->info;
        $thread = $post->thread;
        if(!$thread){abort(404);}// 不存在的内容不能修改
        if(!$thread->channel()->allow_edit&&!auth('api')->user()->isAdmin()){abort(403,'版块不允许修改');}// 不允许修改的版块，非管理员不能修改
        if($post->user_id!=auth('api')->id()&&!auth('api')->user()->isAdmin()){abort(403,'非本人回帖');} // 非本人的回帖，非管理员不能修改
        if($thread->no_reply&&$thread->user_id!=auth('api')->id()&&!auth('api')->user()->isAdmin()){abort(403,'全楼禁止回复');}// 禁止回复状态下，非楼主的回帖，非管理员不能修改
        if(!$thread->is_public&&$thread->user_id!=auth('api')->id()&&!auth('api')->user()->isAdmin()){abort(403,'内容未公开');}// 未公开的状态下，非楼主的回帖，非管理员不能修改
        if($thread->is_locked&&!auth('api')->user()->isAdmin()){abort(403,'内容锁定');} // 锁定状态下，非管理员不能修改

        $post_data = $this->generateUpdatePostData($post,$thread);
        $info_data = $this->generateUpdatePostInfoData($post_data, $thread, $post);
        $post_data = $this->validateBianyuan($post_data, $thread);
        $old_post = $post;
        $post = DB::transaction(function()use($post, $info, $post_data, $info_data){
            $post->update($post_data);
            if($info){
                $info->update($info_data);
            }
            return $post;
        });
        $this->check_length($old_post,$post);
        return $post;
    }

}
