<?php

namespace App\Sosadfun\Traits;

use App\Models\Post;
use StringProcess;
use Carbon;

trait GeneratePostDataTraits{
    use PostObjectTraits;

    public function generatePostData($thread)
    {
        $data = $this->only('body','brief','title','type');
        if(in_array($this->type, config('constants.owner_component_types'))&&$thread->user_id!=auth('api')->id()){abort(403);}
        if(!in_array($this->type,config('constants.all_post_types'))){abort(422,'post_type not allowed');}
        $data['body'] = StringProcess::check_html_tag($data['body']);
        if($this->isDuplicatePost($data)){
            abort(409,'请求已登记，请耐心等待缓存更新，无需重复提交相同数据');
        }
        if(!$this->brief){$data['brief']=StringProcess::trimtext($data['body'], 45);}
        $data['creation_ip'] = request()->ip();
        $data['char_count'] = mb_strlen($data['body']);
        $data['len'] = $this->check_len($data['char_count']);
        $data['use_markdown']=$this->use_markdown ? true:false;
        $data['use_indentation']=$this->use_indentation ? true:false;
        $data['user_id']=auth()->id();
        $data['thread_id']= $thread->id;
        $data['is_anonymous']=0;
        if($this->is_comment&&$this->reply_to_id>0){$data['is_comment']=1;}

        if($this->is_anonymous&&$thread->channel()->allow_anonymous){
            $data['is_anonymous']=1;
            $data['majia']=$this->majia;
        }
        // 如果是chapter等component，根据thread的状态，修改anonymous状态
        if(in_array($this->type, config('constants.owner_component_types'))&&$thread->is_anonymous){
            $data['is_anonymous']=1;
            $data['majia']=$thread->majia;
        }

        if($this->is_bianyuan||$thread->is_bianyuan){
            $data['is_bianyuan']=true;
        }

        if($thread->channel()->type==='box'&&$thread->user_id!=auth()->id()&&$this->reply_to_id===0){
            $data['type']='question';
        }

        if($thread->channel()->type==='homework'&&$thread->user_id!=auth()->id()&&$this->reply_to_id===0&&$data['char_count']>config('homework.critique_char_min')){
            $data['type']='critique';
        }
        return $data;
    }

    public function generatePostInfoData($post_data, $thread)
    {
        if(!in_array($post_data['type'], config('constants.with_info_component_types'))) {
            return;
        }
        $info_data = $this->only('warning','annotation','rating','reviewee_id','reviewee_type','summary');
        if(array_key_exists('annotation',$info_data)){$info_data['annotation']=StringProcess::check_html_tag($info_data['annotation']);}
        if(array_key_exists('warning',$info_data)){$info_data['warning']=StringProcess::check_html_tag($info_data['warning']);}

        $info_data['abstract']=StringProcess::trimtext($post_data['body'],150);

        $max_order_by = $thread->max_component_order();
        $info_data['order_by'] = $max_order_by ? $max_order_by+1 : 1;

        if($this->summary&&!in_array($this->summary,['recommend'])){
            $info_data['summary']=null;
        }


        if($this->reviewee_id==$thread->id&&$this->reviewee_type==='thread'){
            $info_data['reviewee_id']=0;
            $info_data['reviewee_type']='';
        }

        return $info_data;
    }

    public function isDuplicatePost($data)
    {
        $last_post = Post::onWriteConnection()->where('user_id', auth()->id())
        ->orderBy('id', 'desc')
        ->first();
        return !empty($last_post) && strcmp($last_post->body, $data['body']) === 0;
    }

    public function addReplyData($data, $thread)
    {
        if($this->reply_to_id>0){
            $reply = $this->findPost($this->reply_to_id);
            if($reply){
                $data['reply_to_id'] = $reply->id;
                $data['reply_to_brief'] = $this->reply_to_brief??$reply->brief;
                $data['reply_to_position'] = $this->reply_to_position??0;
                $data['is_bianyuan']=$this->is_bianyuan||$reply->is_bianyuan;
                $data['in_component_id'] = $reply->in_component_id>0?$reply->in_component_id:$reply->id;
                if(($reply->type==='post'&&$data['char_count']<50)||$this->is_comment){
                    $data['is_comment'] = 1;
                }
                if($reply->type==='question'&&$thread->user_id===auth()->id()){
                    $data['type'] = 'answer';
                }
                if($reply->type==='work'&&$thread->user_id!=auth()->id()){
                    $data['type'] = 'critique';
                }
            }
        }
        return $data;
    }

    public function generateUpdatePostData($post, $thread)
    {
        $data = $this->only('body','brief','title');
        $data['body'] = StringProcess::check_html_tag($data['body']);
        if(!$this->brief){$data['brief']=StringProcess::trimtext($data['body'], 45);}
        $data['char_count'] = mb_strlen($data['body']);
        $data['len'] = $this->check_len($data['char_count']);
        $data['use_markdown']=$this->use_markdown ? true:false;
        $data['use_indentation']=$this->use_indentation ? true:false;
        $data['edited_at'] = Carbon::now();

        if($this->is_anonymous&&$thread->channel()->allow_anonymous){
            $data['is_anonymous']=1;
            $data['majia']=$this->majia;
        }
        // 如果是chapter等component，根据thread的状态，修改anonymous状态
        if(in_array($this->type, config('constants.owner_component_types'))&&$thread->is_anonymous){
            $data['is_anonymous']=1;
            $data['majia']=$thread->majia;
        }

        if($this->is_bianyuan||$thread->is_bianyuan){
            $data['is_bianyuan']=true;
        }

        if($post->reply_to_id>0&&$post->type==="post"){
            $data['is_comment'] = $this->is_comment? 1:0;
        }

        return $data;
    }

    public function generateUpdatePostInfoData($post_data, $thread, $post)
    {
        if(array_key_exists('type', $post_data)&&!in_array($post_data['type'], config('with_info_component_types'))) {
            return;
        }
        $info_data = $this->only('warning','annotation','rating','reviewee_id','reviewee_type','summary');

        if(array_key_exists('annotation',$info_data)){$info_data['annotation']=StringProcess::check_html_tag($info_data['annotation']);}
        if(array_key_exists('warning',$info_data)){$info_data['warning']=StringProcess::check_html_tag($info_data['warning']);}

        $info_data['abstract']=StringProcess::trimtext($post_data['body'],150);

        if($this->summary&&!in_array($this->summary,['recommend'])){
            $info_data['summary']=null;
        }

        if($this->reviewee_id==$thread->id&&$this->reviewee_type==='thread'){
            $info_data['reviewee_id']=0;
            $info_data['reviewee_type']='';
        }

        return $info_data;
    }

    public function check_length($old_post,$post)
    {
        if($old_post->char_count>config('constants.longcomment_length')&&$post->char_count<config('constants.longcomment_length'))
        $post->user->retract('reduce_long_to_short');
    }

    public function validateBianyuan($post_data, $thread){
        // 如果整个书评楼都是边限，这个评也属于边限
        if($thread->is_bianyuan){
            $post_data['is_bianyuan']=true;
        }
        // 如果被推荐对象是站内文章，且是边缘文，需要增加边缘标记
        if($this->reviewee_id&&$this->reviewee_type==='thread'){
            $reviewee = $this->findThread($info_data['reviewee_id']);
            if($reviewee&&$reviewee->is_bianyuan){
                $post_data['is_bianyuan']=true;
            }
        }
        return $post_data;
    }

    public function check_len($len=0)
    {
        if($len<=0){
            return 0;
        }
        if($len<50){
            return 1;
        }
        if($len<200){
            return 1;
        }
        return 2;
    }
}
