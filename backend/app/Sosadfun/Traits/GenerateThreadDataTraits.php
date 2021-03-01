<?php

namespace App\Sosadfun\Traits;

use App\Models\Thread;
use StringProcess;
use Carbon;

trait GenerateThreadDataTraits{

    public function generateThreadData($channel)
    {
        $thread_data = $this->only('title','brief','body');
        $thread_data['creation_ip'] = request()->getClientIp();
        $thread_data['channel_id']=$channel->id;
        $thread_data['is_anonymous']=0;
        $thread_data['no_reply']=$this->no_reply ? true:false;
        $thread_data['use_markdown']=$this->use_markdown ? true:false;
        $thread_data['use_indentation']=$this->use_indentation ? true:false;
        $thread_data['is_bianyuan']=$this->is_bianyuan? true:false;
        $thread_data['is_public']=$this->is_public ? true:false;
        $thread_data['responded_at']=Carbon::now();
        $thread_data['user_id'] = auth()->id();

        // 将boolean值赋予提交的设置
        if ($this->is_anonymous&&$channel->allow_anonymous){
            $thread_data['is_anonymous']=1;
            $thread_data['majia']=$this->majia;
        }
        $thread_data = $this->convert_to_allowed_thread_format($thread_data, $channel);

        if ($this->isDuplicateThread($thread_data)){
            abort(409,'你已经成功建立相关主题，请耐心等待缓存更新，从个人主页找到已经建立的内容，无需重复建立主题');
        }
        return $thread_data;
    }

    public function isDuplicateThread($thread_data)
    {
        $last_thread = Thread::onWriteConnection()->where('user_id', auth()->id())
        ->where('created_at','>',Carbon::now()->subDays(1))
        ->orderBy('id', 'desc')
        ->first();
        return  !empty($last_thread) && strcmp($last_thread->title, $thread_data['title']) === 0;
    }

    public function generateUpdateThreadData($thread)
    {
        $channel = $thread->channel();
        $thread_data = $this->only('title','brief','body');
        $thread_data['is_anonymous']=0;
        $thread_data['no_reply']=$this->no_reply ? true:false;
        $thread_data['use_markdown']=$this->use_markdown ? true:false;
        $thread_data['use_indentation']=$this->use_indentation ? true:false;
        $thread_data['is_bianyuan']=$this->is_bianyuan? true:false;
        $thread_data['is_public']=$this->is_public ? true:false;
        $thread_data['edited_at']=Carbon::now();

        // 将boolean值赋予提交的设置
        if ($this->is_anonymous&&$channel->allow_anonymous){
            $thread_data['is_anonymous']=1;
        }
        $thread_data = $this->convert_to_allowed_thread_format($thread_data, $channel);

        if ($thread->deletion_applied_at){
            $thread_data['no_reply']=1;
        }

        return $thread_data;

    }

    public function convert_to_allowed_thread_format($thread_data, $channel)
    {
        $thread_data['body'] = StringProcess::check_html_tag($thread_data['body']);
        if($channel->type==="book"){
            while(StringProcess::convert_to_title($thread_data['title'])!=$thread_data['title']){
               $thread_data['title'] = StringProcess::convert_to_title($thread_data['title']);
            }
        }else{
            while(StringProcess::convert_to_public($thread_data['title'])!=$thread_data['title']){
               $thread_data['title'] = StringProcess::convert_to_public($thread_data['title']);
            }
        }
        while(StringProcess::convert_to_public($thread_data['brief'])!=$thread_data['brief']){
           $thread_data['brief'] = StringProcess::convert_to_public($thread_data['brief']);
        }
        if(!$thread_data['title']||!$thread_data['brief']){
            abort(488,'标题简介违规');
        }
        return $thread_data;
    }

    public function generateTongrenData($channel){
        if($channel->id<>2){return;}
        return $this->only('tongren_yuanzhu_tag_id', 'tongren_CP_tag_id', 'tongren_yuanzhu', 'tongren_CP');
    }

}
