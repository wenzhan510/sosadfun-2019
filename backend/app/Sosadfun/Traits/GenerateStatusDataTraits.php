<?php

namespace App\Sosadfun\Traits;

use App\Models\Status;
use StringProcess;
use Carbon;

trait GenerateStatusDataTraits{
    use StatusObjectTraits;
    public function generateStatusData()
    {
        $data['body'] = StringProcess::check_html_tag($this->body);
        if ($this->isDuplicateStatus($data)){
            abort(409,'请求已登记，请耐心等待缓存更新，无需重复提交相同数据');
        }
        $data['brief']=StringProcess::trimtext($data['body'], 45);
        $data['creation_ip'] = request()->ip();
        $data['user_id']=auth()->id();
        $data['no_reply']=$this->no_reply? true:false;
        return $data;
    }

    public function isDuplicateStatus($data)
    {
        $last_status = Status::onWriteConnection()->where('user_id', auth()->id())
        ->orderBy('id', 'desc')
        ->first();
        return !empty($last_status) && strcmp($last_status->body, $data['body']) === 0;
    }

    public function addAttachableData($data)
    {
        if($this->attachable_id&&$this->attachable_type&&in_array($this->attachable_type,['status','thread','post','quote'])){
            $data['attachable_id'] = $this->attachable_id;
            $data['attachable_type'] = $this->attachable_type;
        }
        return $data;
    }

    public function addReplyData($data)
    {
        if($this->reply_to_id>0){
            $reply = $this->statusProfile($this->reply_to_id);
            if($reply&&!$reply->no_reply){
                $data['reply_to_id'] = $reply->id;
            }
        }
        return $data;
    }
}
