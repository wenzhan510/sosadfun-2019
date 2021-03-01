<?php
namespace App\Sosadfun\Traits;
use StringProcess;
use Carbon;
use Auth;
use ConstantObjects;

trait AdminManageTraits{
    use FindModelTrait;

    public function update_report($request)
    {
        $post = \App\Models\Post::find($request->report_post_id);
        if(!$post||$post->type!='case'){abort(403);}
        $info = $post->info;
        if(!$info){abort(403);}
        if($info->reviewee_type!=$request->content_type||$info->reviewee_id!=$request->content_id){abort(403);}

        $info->summary = array_key_exists($request->report_summary, config('constants.report_case_summary'))? $request->report_summary:null;
        $info->save();
        $this->clearPost($request->report_post_id);
        return $post;
    }

    public function find_content($request)
    {
        return $this->findModel(
            $request->content_type,
            $request->content_id,
            array('post','thread','quote','status','user')
        );
    }

    public function content_N_user_management($request)
    {
        $task = '';
        if($request->report_post_id&&$request->report_summary!='approve'){
            return ;// 如果是举报，但并非通过举报，不处理。
        }
        $content = $this->find_content($request);

        if($content){
            if($request->content_fold){
                $task .= $this->content_fold($request->content_type, $content);
            }
            if($request->content_unfold){
                $task .= $this->content_unfold($request->content_type, $content);
            }
            if($request->content_is_bianyuan){
                $task .= $this->content_is_bianyuan($request->content_type, $content);
            }
            if($request->content_not_bianyuan){
                $task .= $this->content_not_bianyuan($request->content_type, $content);
            }
            if($request->content_not_public){
                $task .= $this->content_not_public($request->content_type, $content);
            }
            if($request->content_is_public){
                $task .= $this->content_is_public($request->content_type, $content);
            }
            if($request->content_no_reply){
                $task .= $this->content_no_reply($request->content_type, $content);
            }
            if($request->content_allow_reply){
                $task .= $this->content_allow_reply($request->content_type, $content);
            }
            if($request->content_lock){
                $task .= $this->content_lock($request->content_type, $content);
            }
            if($request->content_unlock){
                $task .= $this->content_unlock($request->content_type, $content);
            }
            if($request->content_change_channel){
                $task .= $this->content_change_channel($request->content_type, $content, $request->content_change_channel_id);
            }
            if($request->content_pull_up){
                $task .= $this->content_pull_up($request->content_type, $content);
            }
            if($request->content_pull_down){
                $task .= $this->content_pull_down($request->content_type, $content);
            }
            if($request->add_tag){
                $task .= $this->add_tag($request->content_type, $request->add_tag, $content);
            }
            if($request->remove_tag){
                $task .= $this->remove_tag($request->content_type, $request->remove_tag, $content);
            }
            if($request->add_tag_to_all_components){
                $task .= $this->add_tag_to_all_components($request->content_type, $request->add_tag_to_all_components, $content);
            }
            if($request->remove_tag_from_all_components){
                $task .= $this->remove_tag_from_all_components($request->content_type, $request->remove_tag_from_all_components, $content);
            }
            if($request->content_remove_anonymous){
                $task .= $this->content_remove_anonymous($request->content_type, $content);
            }
            if($request->content_is_anonymous){
                $task .= $this->content_is_anonymous($request->content_type, $request->majia, $content);
            }
            if($request->content_type_change){
                $task .= $this->content_type_change($request->content_type, $request->content_type_change_to, $content);
            }
            if($request->content_delete){
                $to_be_deleted = $content;
                $task .= $this->content_delete($to_be_deleted);
            }

        }
        $user = \App\Models\User::find($request->content_user_id);
        if($user){
            $user_task = '';

            if($request->user_no_posting_days>0){
                $user_task .= $this->user_no_posting($request->user_no_posting_days, $user);
            }
            if($request->user_allow_posting){
                $user_task .= $this->user_allow_posting($user);
            }
            if($request->user_no_logging_days>0){
                $user_task .= $this->user_no_logging($request->user_no_logging_days, $user);
            }
            if($request->user_allow_logging){
                $user_task .= $this->user_allow_logging($user);
            }
            if($request->user_no_homework_days>0){
                $user_task .= $this->user_no_homework($request->user_no_homework_days, $user);
            }
            if($request->user_allow_homework){
                $user_task .= $this->user_allow_homework($user);
            }
            if($request->user_reset_password){
                $user_task .= $this->user_reset_password($user);
            }
            if($request->user_level_clear){
                $user_task .= $this->user_level_clear($user);
            }
            if($request->user_invitation_clear>0){
                $user_task .= $this->user_invitation_clear($user);
            }
            if($request->user_value_change){
                $user_task .= $this->user_value_change($request->user_value_change, $user);
            }
            if($request->gift_title_id>0){
                $user_task .= $this->gift_title($request->gift_title_id, $user);
            }
            if($request->remove_title_id>0){
                $user_task .= $this->remove_title($request->remove_title_id, $user);
            }
            if($request->send_homework_invitation){
                $user_task .= $this->send_homework_invitation((object)$request->homework_invitation, $user);
            }

            if($user_task){
                $user_task = '用户'.$user_task;
                $task .=$user_task;
            }
        }

        if(!$task){return;}

        if($request->report_post_id){$task = '举报受理：'.$task;}

        if($request->content_type==='thread'&&$content){
            $this->clearThread($content->id);
        }
        if($request->content_type==='post'&&$content){
            $this->clearPost($content->id);
            // $this->clear_thread_posts_with_query($content->thread_id, $request);
        }

        $administration = \App\Models\Administration::create([
            'user_id' => Auth::id(),
            'task' =>$task,
            'reason' => $request->reason,
            'record' => $this->generate_admin_record($request,$content,$user),
            'administratee_id' => $user?$user->id:0,
            'administratable_type' => $request->content_type,
            'administratable_id' => $request->content_type==="user"? ($user?$user->id:0):($content?$content->id:0),
            'is_public' => $request->record_not_public?false:true,
            'summary' => $request->administration_summary??null,
            'report_post_id' => $request->report_post_id?$request->report_post_id:0,
        ]);

        if($administration&&$user){
            $user->remind('new_administration');
        }

        return $administration;
    }

    public function report_management($request, $report_post)
    {
        $task = '';
        if(!$request->report_post_id||$request->report_summary==='approve'){
            return ;// 如果同意举报，不处理
        }
        if(!$report_post){return;} // 找不到report_post的话后面也不继续了

        $user = \App\Models\User::find($report_post->user_id);

        if($user){
            $user_task = '';

            if($request->report_post_fold){
                $user_task .= '举报内容'.$this->content_fold('post', $report_post);
            }

            if($request->reporter_no_posting_days>0){
                $user_task .= $this->user_no_posting($request->reporter_no_posting_days, $user);
            }
            if($request->reporter_no_logging_days>0){
                $user_task .= $this->user_no_logging($request->reporter_no_logging_days, $user);
            }
            if($request->reporter_level_clear){
                $user_task .= $this->user_level_clear($user);
            }
            if($request->reporter_invitation_clear){
                $user_task .= $this->user_invitation_clear($user);
            }
            if($request->reporter_value_change){
                $user_task .= $this->user_value_change($request->reporter_value_change, $user);
            }

            if($user_task){
                $user_task = '举报者'.$user_task;
                $task .=$user_task;
            }
        }

        if(!$task){return;}

        $task = '举报行为管理：'.$task;

        $administration = \App\Models\Administration::create([
            'user_id' => Auth::id(),
            'task' =>$task,
            'reason' => $request->reason,
            'record' => $this->generate_record('post',$report_post),
            'administratee_id' => $user?$user->id:0,
            'administratable_type' => 'post',
            'administratable_id' => $report_post->id,
            'is_public' => $request->record_not_public?false:true,
            'summary' => $request->administration_summary??'',
            'report_post_id' => $request->report_post_id,
        ]);

        if($administration&&$user){
            $user->remind('new_administration');
        }

        return $administration;
    }

    private function generate_admin_record($request, $content, $user)
    {
        if($user&&!$content){
            return $this->generate_record('user', $user);
        }
        if($content){
            return $this->generate_record($request->content_type, $content);
        }
    }
    private function generate_record($type, $item){
        switch ($type){
            case 'user':
                return $item->name;
                break;
            case 'thread':
                return StringProcess::trimtext('《'.$item->title."》".$item->brief, 160);
                break;
            case 'post':
                return StringProcess::trimtext($item->brief, 160);
                break;
            case 'status':
                return StringProcess::trimtext($item->brief, 160);
                break;
            case 'quote':
                return StringProcess::trimtext($item->body, 160);
                break;
            default:
            return 'no record';
        }
    }
    private function content_type_change($content_type, $change_to_type, $content)
    {
        if($content->type!=$change_to_type&&$content_type==='post'&&in_array($change_to_type, config('constants.post_types'))){
            $old_type = $content->type;
            $content->type = $change_to_type;
            $content->save();
            $new_type = $content->type;
            if(in_array($new_type, config('constants.post_types_with_info'))){
                \App\Models\PostInfo::firstOrCreate(['post_id'=>$content->id]);
            }
            return "修改回帖类型:".config('constants.post_types')[$old_type]."=>".config('constants.post_types')[$new_type]."|";
        }
        return;
    }

    private function content_fold($content_type,$content)
    {
        if(in_array($content_type, ['post'])&&$content->fold_state===0){
            $content->fold_state = 1;
            $content->save();
            return "折叠|";
        }
        return;
    }
    private function content_unfold($content_type,$content)
    {
        if(in_array($content_type, ['post'])&&$content->fold_state>0){
            $content->fold_state = 0;
            $content->save();
            return "解除折叠|";
        }
        return;
    }
    private function content_is_bianyuan($content_type,$content)
    {
        if(in_array($content_type, ['thread','post'])&&!$content->is_bianyuan){
            $content->is_bianyuan = 1;
            $content->save();
            return "转为边限|";
        }
        return;
    }
    private function content_not_bianyuan($content_type,$content)
    {
        if(in_array($content_type, ['thread','post'])&&$content->is_bianyuan){
            $content->is_bianyuan = 0;
            $content->save();
            return "转为非边限|";
        }
        return;
    }

    private function content_not_public($content_type, $content)
    {
        if(in_array($content_type, ['thread','status'])&&$content->is_public){
            $content->is_public = 0;
            $content->save();
            return "隐藏|";
        }
        return;
    }
    private function content_is_public($content_type, $content)
    {
        if(in_array($content_type, ['thread','status'])&&!$content->is_public){
            $content->is_public = 1;
            $content->save();
            return "公开|";
        }
        return;
    }
    private function content_no_reply($content_type, $content)
    {
        if(in_array($content_type, ['thread','status'])&&!$content->no_reply){
            $content->no_reply = 1;
            $content->save();
            return "禁止回复|";
        }
        return;
    }
    private function content_allow_reply($content_type, $content)
    {
        if(in_array($content_type, ['thread','status'])&&$content->no_reply){
            $content->no_reply = 0;
            $content->save();
            return "允许回复|";
        }
        return;
    }
    private function content_lock($content_type, $content)
    {
        if(in_array($content_type, ['thread'])&&!$content->is_locked){
            $content->is_locked = 1;
            $content->save();
            return "锁定|";
        }
        return;
    }
    private function content_unlock($content_type, $content)
    {
        if(in_array($content_type, ['thread'])&&$content->is_locked){
            $content->is_locked = 0;
            $content->save();
            return "解除锁定|";
        }
        return;
    }
    private function content_change_channel($content_type, $content, $channel_id)
    {
        if(in_array($content_type, ['thread'])&&is_numeric($channel_id)&&$channel_id>0&&$channel_id!=$content->channel_id){
            $old_channel = $content->channel();
            $content->channel_id = $channel_id;
            $content->save();
            $new_channel = $content->channel();
            return "转移主题:".$old_channel->channel_name."=>".$new_channel->channel_name."|";
        }
        return;
    }
    private function content_pull_up($content_type, $content)
    {
        if(in_array($content_type, ['thread'])){
            $content->update(['responded_at'=>Carbon::now()]);
            return "内容上浮|";
        }
        return;
    }
    private function content_pull_down($content_type, $content)
    {
        if(in_array($content_type, ['thread'])){
            $content->update(['responded_at'=>Carbon::now()->subMonths(6)]);
            return "内容下沉|";
        }
        return;
    }
    private function content_delete($content)
    {
        $content->delete();
        return "内容删除|";
    }
    private function find_tag_by_id_or_name($tag_info)
    {
        if(is_numeric($tag_info)){
            $tag = ConstantObjects::find_tag_by_id($tag_info);
        }else{
            $tag = ConstantObjects::find_tag_by_name($tag_info);
        }
        return $tag;
    }
    private function content_add_tag($content_type, $tag, $content)
    {
        if(in_array($content_type, ['thread','post'])){
            $content->tags()->syncWithoutDetaching($tag->id);
            if($tag->tag_type==='编推'){
                if($content_type==='post'&&$content->info&&$content->info->reviewee){
                    $content->info->update(['summary'=>'editorRec']);
                    $content->info->reviewee->update(['recommended'=>1]);
                }
                if($content_type==='thread'){
                    $content->update(['recommended'=>1]);
                }
            }
            return "添加标签:".$tag->tag_name."|";
        }
        return;
    }
    private function content_remove_tag($content_type, $tag, $content)
    {
        if(in_array($content_type, ['thread','post'])){
            $content->tags()->detach($tag->id);
            if($tag->tag_type==='编推'){
                if($content_type==='post'&&$content->info&&$content->info->reviewee){
                    $content->info->update(['summary'=>'']);
                    $content->info->reviewee->update(['recommended'=>0]);
                }
                if($content_type==='thread'){
                    $content->update(['recommended'=>0]);
                }
            }
            return "去除标签:".$tag->tag_name."|";
        }
        return;
    }
    private function add_tag($content_type, $tag_info, $content)
    {
        $tag = $this->find_tag_by_id_or_name($tag_info);

        if(!$tag){return;}

        return $this->content_add_tag($content_type, $tag, $content);

    }
    private function remove_tag($content_type, $tag_info, $content)
    {
        $tag = $this->find_tag_by_id_or_name($tag_info);

        if(!$tag){return;}

        return $this->content_remove_tag($content_type, $tag, $content);
    }
    private function add_tag_to_all_components($content_type, $tag_info, $content)
    {
        $tag = $this->find_tag_by_id_or_name($tag_info);

        if(!$tag){return;}

        if($content_type==='thread'&&$content->channel()->type==="list"){
            $posts = $content->posts()->with('info.reviewee')->withType('review')->get();
            foreach($posts as $post){
                $this->content_add_tag('post', $tag, $post);
            }
        }

        return "全楼".$this->content_add_tag($content_type, $tag, $content);
    }
    private function remove_tag_from_all_components($content_type, $tag_info, $content)
    {
        $tag = $this->find_tag_by_id_or_name($tag_info);

        if(!$tag){return;}

        if($content_type==='thread'&&$content->channel()->type==="list"){
            $posts = $content->posts()->with('info.reviewee')->withType('review')->get();
            foreach($posts as $post){
                $this->content_remove_tag('post', $tag, $post);
            }
        }

        return "全楼".$this->content_remove_tag($content_type, $tag, $content);
    }
    private function content_remove_anonymous($content_type, $content)
    {
        if(in_array($content_type, ['thread','post','quote'])&&$content->is_anonymous){
            $content->is_anonymous = false;
            $content->save();
            return "掀马甲|";
        }


    }
    private function content_is_anonymous($content_type, $majia="匿名咸鱼", $content)
    {
        if(in_array($content_type, ['thread','post','quote'])&&!$content->is_anonymous){
            $content->is_anonymous = 1;
            $content->majia = $majia;
            $content->save();
            return "披上马甲|";
        }


    }

    private function user_no_posting($days, $user)
    {
        $info = $user->info;
        if($user&&$info&&$days&&is_numeric($days)&&$days>0){
            $user->no_posting = 1;
            $info = $user->info;
            $info->no_posting_until = $info->no_posting_until>Carbon::now() ? $info->no_posting_until->addDays($days) : Carbon::now()->addDays($days);
            $user->save();
            $info->save();
            return "禁言".$days."天|";
        }
        return;
    }
    private function user_allow_posting($user)
    {
        $info = $user->info;
        if($user&&$info&&$user->no_posting){
            $user->no_posting = 0;
            $info->no_posting_until = Carbon::now();
            $user->save();
            $info->save();
            return "解除禁言|";
        }
    }
    private function user_no_logging($days, $user)
    {
        $info = $user->info;
        if($user&&$info&&$days&&is_numeric($days)&&$days>0){
            $user->no_logging = 1;
            $info = $user->info;
            $info->no_logging_until = $info->no_logging_until>Carbon::now() ? $info->no_logging_until->addDays($days) : Carbon::now()->addDays($days);
            $user->save();
            $info->save();
            return "禁止登陆".$days."天|";
        }
        return;
    }
    private function user_allow_logging($user)
    {
        $info = $user->info;
        if($user&&$info&&$user->no_logging){
            $user->no_logging = 0;
            $info->no_logging_until = Carbon::now();
            $user->save();
            $info->save();
            return "允许登陆|";
        }
    }
    private function user_no_homework($days, $user)
    {
        $info = $user->info;
        if($user&&$info&&$days&&is_numeric($days)&&$days>0){
            $user->no_homework = 1;
            $info = $user->info;
            $info->no_homework_until = $info->no_homework_until>Carbon::now() ? $info->no_homework_until->addDays($days) : Carbon::now()->addDays($days);
            $user->save();
            $info->save();
            return "作业禁令".$days."天|";
        }
        return;
    }
    private function user_allow_homework($user)
    {
        $info = $user->info;
        if($user&&$info&&$user->no_homework){
            $user->no_homework = 0;
            $info->no_homework_until = Carbon::now();
            $user->save();
            $info->save();
            return "允许使用作业区|";
        }
    }
    private function user_reset_password($user)
    {
        $user->admin_reset_password();
        return "系统监测到账户安全风险密码重置";
    }

    private function user_level_clear($user)
    {
        $info = $user->info;
        if($user&&$info){
            $user->level = 0;
            $user->quiz_level = 0;
            $info = $user->info;
            $info->salt = 0;
            $info->fish = 0;
            $info->token_limit = 0;
            $user->save();
            $info->save();
            return "等级与虚拟物清零|";
        }
    }

    private function user_invitation_clear($user)
    {
        $info = $user->info;
        if($user&&$info){
            $info->token_limit = 0;
            $user->save();
            $info->save();
            return "邀请额度清零|";
        }
    }

    private function user_value_change($user_value_change, $user)
    {
        $task = '';
        $info = $user->info;
        if(!$info){return;}

        if(array_key_exists('salt',$user_value_change)&&is_numeric($user_value_change['salt'])&&$user_value_change['salt']!=0){
            $info->salt+=$user_value_change['salt'];
            $task.='盐粒'.$user_value_change['salt']."|";
        }

        if(array_key_exists('fish',$user_value_change)&&is_numeric($user_value_change['fish'])&&$user_value_change['fish']!=0){
            $info->fish+=$user_value_change['fish'];
            $task.='咸鱼'.$user_value_change['fish']."|";
        }

        if(array_key_exists('ham',$user_value_change)&&is_numeric($user_value_change['ham'])&&$user_value_change['ham']!=0){
            $info->ham+=$user_value_change['ham'];
            $task.='火腿'.$user_value_change['ham']."|";
        }

        if(array_key_exists('level',$user_value_change)&&is_numeric($user_value_change['level'])&&$user_value_change['level']!=0){
            $user->level+=$user_value_change['level'];
            $task.='等级'.$user_value_change['level']."|";
        }

        if(array_key_exists('token_limit',$user_value_change)&&is_numeric($user_value_change['token_limit'])&&$user_value_change['token_limit']!=0){
            $info->token_limit+=$user_value_change['token_limit'];
            $task.='邀请码额度'.$user_value_change['token_limit']."|";
        }
        $user->save();
        $info->save();
        return $task;
    }
    private function gift_title($title_id, $user)
    {
        $title = ConstantObjects::find_title_by_id($title_id);
        if(!$title){return;}

        $user->titles()->syncWithoutDetaching($title->id);
        return "赠予头衔:".$title->name."|";
    }
    private function remove_title($title_id, $user)
    {
        $title = ConstantObjects::find_title_by_id($title_id);
        if(!$title){return;}

        $user->titles()->detach($title->id);
        return "去除头衔:".$title->name."|";
    }
    private function send_homework_invitation($homework_invitation, $user)
    {
        $homeworkInvitation = $user->createHomeworkInvitation($homework_invitation->homework_id, $homework_invitation->homework_level, $homework_invitation->homework_role, $homework_invitation->valid_days);

        if($homeworkInvitation){
            return "发放作业邀请券|";
        }
        return;
    }
}
