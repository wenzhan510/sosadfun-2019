<?php

namespace App\Models\Traits;

use StringProcess;
use App\Models\Tongren;
use Carbon;
use ConstantObjects;

trait ThreadTongrenTraits{
    public function tongren_data_sync($data)
    {
        $tongren = Tongren::on('mysql::write')->find($this->id);
        $this->drop_tongren_tags();

        // 不是同人的情况下，去掉同人相关的tag，去掉同人信息
        if($this->channel_id!=2){
            if($tongren){
                $tongren->delete();
            }
            return;
        }

        // 是同人的情况下，先统计同人信息
        $tongren_tags = [];
        // 收集原著标记
        if(array_key_exists('tongren_yuanzhu_tag_id', $data)&&$data['tongren_yuanzhu_tag_id']>0){
            $tag = ConstantObjects::find_tag_by_id($data['tongren_yuanzhu_tag_id']);
            if($tag->tag_type==='同人原著'){
                array_push($tongren_tags, $data['tongren_yuanzhu_tag_id']);
            }
        }
        // 收集CP标记
        if(array_key_exists('tongren_CP_tag_id', $data)&&$data['tongren_CP_tag_id']>0){
            $tag = ConstantObjects::find_tag_by_id($data['tongren_CP_tag_id']);
            if($tag->tag_type==='同人CP'){
                array_push($tongren_tags, $data['tongren_CP_tag_id']);
            }
        }
        // 同步已经确定的tag
        $this->tags()->syncWithoutDetaching($tongren_tags);

        // 两个tag都有了，就不需要tongren这个选项了
        if(count($tongren_tags)>=2){
            return;
        }

        // 如果信息不全，什么都不管
        if(!array_key_exists('tongren_CP', $data)||!array_key_exists('tongren_yuanzhu', $data)){
            return;
        }

        // 前面都正常，就缺已有tag，需要改tongren文字信息
        if($tongren){
            $tongren->update([
                'tongren_yuanzhu' => $data['tongren_yuanzhu'],
                'tongren_CP' => $data['tongren_CP'],
            ]);
        }else{
            Tongren::create([
                'thread_id' => $this->id,
                'tongren_yuanzhu' => $data['tongren_yuanzhu'],
                'tongren_CP' => $data['tongren_CP'],
            ]);
        }
        return;
    }

}
