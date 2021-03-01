<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CacheUser;
use Cache;
use DB;
use Carbon;
use ConstantObjects;
use App\Events\NewPost;

use App\Models\HomeworkRegistration;
use App\Models\HomeworkInvitation;
use App\Models\HomeworkPurchase;
use App\Models\Homework;
use App\Models\Thread;
use App\Models\Post;

use App\Http\Requests\StoreHomework;
use App\Http\Requests\StoreHomeworkThread;
use App\Http\Requests\StoreHomeworkPost;

use App\Sosadfun\Traits\HomeworkObjectTraits;
use App\Sosadfun\Traits\ThreadObjectTraits;
use App\Sosadfun\Traits\PostObjectTraits;


class HomeworkController extends Controller
{
    use HomeworkObjectTraits;
    use ThreadObjectTraits;
    use PostObjectTraits;

    public function __construct()
    {
        $this->middleware(['auth:api', 'no_homework_control']);
        $this->middleware('admin')->only('store', 'update', 'destroy', 'deactivate', 'send_reward', 'manage_registration');
    }

    public function index(Request $request)
    {
        $is_finished = $request->is_finished? true:false;
        if($is_finished){
            $homeworks = $this->findFinishedHomeworks();
        }else{
            $homeworks = $this->findActiveHomeworks();
        }
        return ;
        // TODO return homeworks
    }

    public function userHomework($id)
    {
        if(!auth('api')->user()->isAdmin()){
            $id = auth('api')->id();
        }
        $user = CacheUser::user($id);
        $info = CacheUser::info($id);
        if(!$user||!$info){abort(404);}

        $homeworks = $this->findActiveHomeworks();
        $active_registrations = $user->active_registrations();
        $past_registrations = $user->past_registrations();
        $active_invitations = $user->active_homework_invitations();
        return ;
        //TODO return homework resourece
    }

    public function userHomeworkInvitation($id)
    {
        if(!auth('api')->user()->isAdmin()){
            $id = auth('api')->id();
        }
        $user = CacheUser::user($id);
        $info = CacheUser::info($id);
        if(!$user||!$info){abort(404);}

        $homework_invitations = $user->homework_invitations();
        return ;
        //TODO return homework invitation resourece
    }

    public function show($id)
    {
        $homework = $this->findHomeworkProfile($id);
        if(!$homework){abort(404);}
        return ;
        //TODO return homework invitation resourece
    }

    public function store(StoreHomework $form)
    {
        $thread = $form->storeHomeworkandThread(); // TODO this needs adjustment
        $homework_tag = ConstantObjects::find_tag_by_name('读写活动');
        $thread->tags()->syncWithoutDetaching($homework_tag->id);
        //TODO return homework invitation resourece
    }

    public function update(Homework $homework, StoreHomework $form)
    {
        $homework = $form->updateHomework($homework);
        $this->refreshHomework($homework->id);
        // TODO 修改返回信息
        // return redirect()->route('homework.show', $homework->id)->with("success", "你已成功修改作业");
    }

    public function destroy(Homework $homework)
    {
        // TODO
    }


    public function register($id, Request $request)
    {

        $homework = $this->findHomeworkProfile($id);

        // TODO 注册作业活动

        // $user = CacheUser::Auser();
        // $info = CacheUser::Ainfo();
        //
        // $requirement = config('homework.levels')[$homework->level];
        //
        // $this->validate($request, [
        //     'role' => 'required|string|max:10',
        // ]);
        //
        // $role = $request->role;
        //
        // if( in_array($role, ['worker', 'critic']) ){
        //     $this->validate($request, [
        //         'majia' => 'required|string|max:10',
        //     ]);
        // }
        //
        // if( !in_array($role, ['worker', 'critic', 'watcher', 'reader']) ){abort(422, '输入信息不符');}
        //
        // if( in_array($role, ['worker', 'critic', 'watcher']) && !$homework->is_active ){
        //     return back()->with('danger','作业已关闭');
        // } // 作业状态不符合
        //
        // if( in_array($role, ['worker', 'critic', 'watcher']) && Auth::user()->participatingThisActiveHomework($homework->id) ){
        //     return back()->with('warning','已参与作业活动');
        // } // 作业状态不符合
        //
        // if( in_array($role, ['worker', 'critic']) && $homework->{$role.'_registration_limit'}===0 ){
        //     return back()->with('warning','参与人数已达上限');
        // } //注册已满
        //
        // if( in_array($role, ['worker', 'critic']) && preg_match('/匿名咸鱼/', $request->majia)){
        //     return back()->with('warning', '请不要使用匿名咸鱼作为报名马甲');
        // }
        //
        // if( in_array($role, ['watcher', 'reader']) && $user->canSeeAnyHomework() ){
        //     return back()->with('info','作业区资深潜水员无需购买');
        // }
        //
        // if( $role==='reader' && $homework->is_active ){
        //     return back()->with('danger','作业尚未结束');
        // } // 作业状态不符合
        //
        // if( $role==='reader' && Auth::user()->purchasedThisHomework($homework->id) ){
        //     return back()->with('warning','已购买阅读权限,无需重复购买');
        // } // 作业状态不符合
        //
        // if($user->level < $requirement[$role]['level_limit'] || $info->ham < $requirement[$role]['ham_price']){
        //     return back()->with('warning','用户不满足要求');
        // } // 作业等级、火腿要求不符合
        //
        // if( !$user->canSeeAnyHomework() && ($requirement[$role]['invitation_required']) ){
        //     if(!$user->useHomeworkInvitationFor($homework->id, $homework->level, $role)){
        //         return back()->with('warning','邀请券已失效');// 如果需要邀请券，找到它，使用它，缺券的拒绝
        //     }
        // }
        //
        // $info->type_value_change('ham', -$requirement[$role]['ham_price']); //扣火腿
        //
        // if( in_array($role, ['worker', 'critic']) ){
        //     $homework->registedByUser($user, $role, $request->majia);
        //     $user->refreshAciveRegistrations();
        //     $this->refreshActiveHomeworks();
        //     $this->refreshHomework($homework->id);
        //     return redirect()->route('homework.show', $homework->id)->with('success','已成功获得参与作业权限');
        // }
        //
        // if( in_array($role, ['watcher', 'reader']) ){
        //     $homework->purchasedByUser($user);
        //     $user->refreshPurchasedHomeworks();
        //     return redirect()->route('homework.show', $homework->id)->with('success','已成功获得围观阅读作业权限');
        // }

    }

    public function mark_as_finished(HomeworkRegistration $homework_registration, Request $request)
    {
        // $user = CacheUser::Auser();
        // $homework = $homework_registration->homework;
        //
        // if($request->finished!=$homework_registration->title.'已完结'){return back()->with('warning','输入错误');}
        //
        // if(!$homework||!$homework_registration||!$homework->is_active||$homework_registration->role!='worker'||$homework_registration->user_id!=$user->id||$homework_registration->thread_id===0||!$homework_registration->thread){
        //     abort(422);
        // }
        //
        // if($homework_registration->briefThread->briefPosts->where('type','work')->sum('char_count')<1000){return back()->with('warning','作业正文总字数少于1000字，请核实后再确认完结');}
        //
        // $homework_registration->update([
        //     'finished_at' => Carbon::now(),
        // ]);
        //
        // $user->refreshAciveRegistrations();
        // $this->refreshActiveHomeworks();
        // $this->refreshHomework($homework->id);
        // $this->clearThread($homework_registration->thread_id);
        //
        // $homework->massAssignRequiredCritique($homework_registration->thread_id);
        // return back()->with('success','已经成功标记完结');
    }

    public function submit(Homework $homework, StoreHomeworkThread $form)
    {
        // $user = CacheUser::Auser();
        // $homework_registration = $homework->registrations()->where('user_id',$user->id)->first();
        //
        // if(!$user||!$homework_registration||!$homework||!$homework->is_active||!$homework_registration->role==='worker'||$homework_registration->thread_id>0){abort(422);}
        //
        // $thread = $form->generateHomeworkThread($homework, $homework_registration);
        //
        // $homework_tag = ConstantObjects::find_tag_by_name('本次作业'); //本次作业，往期作业，其他作业
        // $thread->tags()->syncWithoutDetaching($homework_tag->id);
        //
        // $thread->user->reward("regular_thread");
        //
        // $this->refreshHomework($homework->id);
        // $this->refreshActiveHomeworks();
        // $user->refreshAciveRegistrations();
        //
        // return redirect()->route('thread.show', $thread->id)->with("success", "你已成功提交作业");
    }

    public function submit_work($id, StoreHomeworkPost $form)
    {
        // $thread = Thread::on('mysql::write')->find($id);
        // if($thread->is_locked||$thread->user_id!=Auth::id()){abort(403);}
        //
        // $homework_registration = $thread->homework_registration;
        // if(!$homework_registration||$homework_registration->user_id!=Auth::id()){abort(422);}
        //
        // $homework = $homework_registration->homework;
        // if(!$homework||!$homework->is_active){abort(422);}
        //
        // $post = $form->generateHomeworkWork($thread);
        //
        // event(new NewPost($post));
        //
        // $msg = $post->reward_check();
        //
        // $this->clearThread($id);
        //
        // return redirect()->route('post.show', $post->id)->with('success', $msg);
    }

    public function deactivate($id)
    {
        // $homework = $this->findHomeworkProfile($id);
        //
        // foreach($homework->registrations as $registration){
        //     $thread = $registration->briefThread;
        //     if($thread){
        //         $homework_tag = ConstantObjects::find_tag_by_name('往期作业'); //本次作业，往期作业，其他作业
        //         $thread->keep_only_admin_tags();
        //         $thread->tags()->syncWithoutDetaching($homework_tag->id);
        //         $thread->update(['is_locked' => 1]);
        //     }
        // }
        //
        // $homework->update([
        //     'is_active' => 0,
        //     'end_at' => Carbon::now(),
        // ]);
        //
        // foreach($homework->registrations as $registration){
        //     $registration->owner->refreshAciveRegistrations();
        // }
        //
        // $this->refreshHomework($homework->id);
        // $this->refreshActiveHomeworks();
        // $this->refreshFinishedHomeworks();
        //
        // return redirect()->route('homework.show',$homework->id)->with('success','已标记作业活动结束');
    }

    public function send_reward(Homework $homework, Request $request)
    {
        // $homework->load('registrations.user');
        //
        // $reward_base = config('homework.levels')[$homework->level]['reward_base'];
        //
        // foreach($homework->registrations as $registration){
        //     if( array_key_exists($registration->user_id, $request->reward)){
        //         switch($request->reward[$registration->user_id]){
        //             case '-2':
        //                 $registration->update(['summary'=>-2]);
        //                 $registration->user->no_homework(2*config('homework.no_homework_base_days')*$reward_base);
        //             break;
        //             case '-1':
        //                 $registration->update(['summary'=>-1]);
        //                 $registration->user->no_homework(config('homework.no_homework_base_days')*$reward_base);
        //             break;
        //             case '1':
        //                 $registration->update(['summary'=>1]);
        //                 $registration->user->reward('homework_regular_'.$registration->role,$reward_base);//需调整分值
        //                 $homework->purchasedByUser($registration->user);
        //             break;
        //             case '2':
        //                 $registration->update(['summary'=>2]);
        //                 $registration->user->reward('homework_excellent_'.$registration->role,$reward_base);//需调整分值
        //                 $homework->purchasedByUser($registration->user);
        //             break;
        //         }
        //     }
        //
        //     if($request->homework_invitation_worker&&in_array($registration->user_id,$request->homework_invitation_worker)){
        //         $registration->user->createHomeworkInvitation(0, 1, 'worker', config('homework.homework_invitation_base_days'));
        //     }
        //
        //     if($request->homework_invitation_critic&&in_array($registration->user_id,$request->homework_invitation_critic)){
        //         $registration->user->createHomeworkInvitation(0, 1, 'critic', config('homework.homework_invitation_base_days'));
        //     }
        // }
        // return redirect()->route('homework.show', $homework->id)->with('success','已成功发放奖励');
    }

    public function manage_registration(HomeworkRegistration $homework_registration, Request $request){
        // $homework = $homework_registration->homework;
        // if(!$homework||!$homework_registration){abort(404);}
        // $this->validate($request, [
        //     'majia' => 'nullable|string|max:10',
        //     'role' => 'required|string|max:10',
        //     'title' => 'nullable|string|max:20',
        //     'thread_id' => 'required|numeric|min:0',
        //     'received_critique_count' => 'required|numeric|min:0',
        //     'given_critique_count' => 'required|numeric|min:0',
        //     'required_critique_thread_id' => 'required|numeric|min:0',
        //     'summary' => 'required|numeric|min:-2|max:2',
        // ]);
        // $data = $request->only('majia','role','title','thread_id','received_critique_count','given_critique_count','required_critique_thread_id','summary');
        // $data['required_critique_done'] = $request->required_critique_done?true:false;
        // if($request->is_finished&&$homework_registration->finished_at == null){
        //     $data['finished_at'] = Carbon::now();
        // }
        // if(!$request->is_finished&&$homework_registration->finished_at){
        //     $data['finished_at'] = null;
        // }
        // $homework_registration->update($data);
        // $this->refreshHomework($homework->id);
        // return redirect()->route('homework.show',$homework->id)->with('success','已成功修改注册详情');
    }
}
