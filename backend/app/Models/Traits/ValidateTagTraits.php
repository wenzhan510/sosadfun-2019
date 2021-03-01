<?php
namespace App\Models\Traits;

use ConstantObjects;

trait ValidateTagTraits{


    public function tags_validate($tags=[])//检查由用户提交的tags组合，是否符合基本要求 $tags is an array [1,2,3]...
    {
        $valid_tags = [];//通过检查的tag
        $limit_count_tags = [];//tag数量限制
        $only_one_tags = [];//只能选一个的tag
        $tags = (array)$tags;
        foreach($tags as $key => $value){
            $tag = ConstantObjects::find_tag_by_id($value);
            if($tag){//首先应该判断这个tag是否存在，否则会报错Trying to get property 'tag_type' of non-object
                if (in_array($tag->tag_type,config('tag.types'))){//一个正常录入的tag，它的type应该在config中能够找到。
                    $error = '';
                    //检查是否为非边缘文章提交了边缘标签
                    if((!$this->is_bianyuan) && $tag->is_bianyuan){
                        $error = 'bianyuan violation';
                    }
                    //如不属于某channel却选择了专属于某channel的tag,如为非同人thread选择了同人channel的tag
                    if(($tag->channel_id>0)&&( $tag->channel_id != $this->channel_id)){
                        $error = 'channel violation';
                    }

                    //检查是否满足某些类tag只能选一个的限制情况，
                    if (in_array($tag->tag_type, config('tag.limits.only_one'))){
                        if(array_key_exists($tag->tag_type, $only_one_tags)){
                            $error = 'only one tag violation';
                        }else{
                            $only_one_tags[$tag->tag_type] = $tag->id;
                        }
                    }

                    //检查数目限制的那些是否满足要求， sum_limit < sum_limit_count
                    if (in_array($tag->tag_type,config('tag.limits.sum_limit'))){
                        if(!empty($limit_count_tags)&&(count($limit_count_tags)>=config('tag.sum_limit_count'))){
                            $error = 'too many tags in total';
                        }else{
                            array_push($limit_count_tags,$tag->id);
                        }
                    }

                    //如果这个tag没有犯上面的任何错误，而且不属于只有管理才能添加的tag，那么通过检验
                    if((!$tag->admin_only())&&($error==='')){
                        array_push($valid_tags, $tag->id);
                    }else{
                        echo($error.', invalid tag id='.$tag->id."\n");//这个信息应该前端保证它不要出现
                    }
                }
            }
        }//循环结束
        return $valid_tags;
    }

    public function drop_tongren_tags() //去掉'同人原著'和"同人CP"这两种tag
    {
        $detach_tags = [];
        foreach($this->tags as $tag){
            if(in_array($tag->tag_type, ['同人原著','同人CP'])){
                array_push($detach_tags,$tag->id);
            }
        }
        if(!empty($detach_tags)){
            $this->tags()->detach($detach_tags);
        }
        return count($detach_tags);
    }

    public function drop_none_tongren_tags() //去掉非管理，非'同人原著'、"同人CP"的，用户在tag页所有可以选择的自主标签
    {
        $detach_tags = [];
        foreach($this->tags as $tag){
            if(in_array($tag->tag_type, config('tag.custom_none_tongren_tag_types'))){
                array_push($detach_tags,$tag->id);
            }
        }
        if(!empty($detach_tags)){
            $this->tags()->detach($detach_tags);
        }
        return count($detach_tags);
    }

    public function keep_only_admin_tags()//去掉所有用户自己提交的tag,返回成功去掉的
    {
        $detach_tags = [];
        foreach($this->tags as $tag){
            if(!in_array($tag->tag_type, config('tag.limits.admin_only'))){
                array_push($detach_tags,$tag->id);
            }
        }
        if(!empty($detach_tags)){
            $this->tags()->detach($detach_tags);
        }
        return count($detach_tags);
    }
}
