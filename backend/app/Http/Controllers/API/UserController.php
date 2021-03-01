<?php

namespace App\Http\Controllers\API;

use Cache;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Thread;
use App\Models\Status;
use App\Models\UserInfo;
use App\Models\UserIntro;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserPreferenceResource;
use App\Http\Resources\UserReminderResource;
use App\Http\Resources\UserIntroResource;
use App\Http\Resources\ThreadBriefResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\StatusResource;
use App\Http\Resources\PaginateResource;
use App\Sosadfun\Traits\UserObjectTraits;
use App\Sosadfun\Traits\CollectionObjectTraits;
use App\Sosadfun\Traits\ListObjectTraits;
use App\Sosadfun\Traits\BoxObjectTraits;
use App\Helpers\CacheUser;

class UserController extends Controller
{
    use UserObjectTraits;
    use CollectionObjectTraits;
    use ListObjectTraits;
    use BoxObjectTraits;

    public function __construct()
    {
        $this->middleware('auth:api')->except('index','show','showThread','showBook','showStatus');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // TODO 展示全站用户列表
        // if($request->page&&!Auth::check()){
        //     return redirect('login');
        // }
        // $queryid = 'UserIndex.'
        // .url('/')
        // .(is_numeric($request->page)? 'P'.$request->page:'P1');
        //
        // $users = Cache::remember($queryid, 10, function () use($request) {
        //     return User::with('title','info')
        //     ->orderBy('qiandao_at','desc')
        //     ->paginate(config('preference.users_per_page'))
        //     ->appends($request->only('page'));
        // });
        //
        // return view('statuses.user_index', compact('users'))->with(['status_tab'=>'user']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user_profile = [
            'user' => CacheUser::user($id),
            'info' => CacheUser::info($id),
        ];
        if (!$user_profile['user'] || !$user_profile['info']) {abort(404);}
        $user_profile['intro'] = $user_profile['info']->has_intro ? CacheUser::intro($id) : null;

        return response()->success(new UserResource($user_profile));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (auth('api')->id() != $id) {abort(403, '非本人不可注销');}

        $user = CacheUser::user($id);
        $user->delete();
        CacheUser::clearuser($id);

        return response()->success([
            'success' => '成功注销用户',
            'user_id' => $id,
        ]);
    }

    public function getReminder($id,Request $request)
    {
        if (!auth('api')->user()->isAdmin() && auth('api')->id() != $id) {abort(403, '不可查看该用户设置');}

        $user = CacheUser::user($id);
        $info = CacheUser::info($id);
        if (!$user || !$info) {abort('404', '用户不存在');}

        return response()->success(new UserReminderResource($info));
    }

    public function updateReminder($id, Request $request)
    {
        if (auth('api')->id() != $id) {abort(403, '不可修改该用户设置');}
        $info = CacheUser::info($id);

        $data = [];
        // 前端返回boolean值，true为将相应reminder清零
        if (Request('unread_reminders')) {$data['unread_reminders'] = 0;}
        if (Request('unread_updates')) {$data['unread_updates'] = 0;}
        if (Request('message_reminders')) {$data['message_reminders'] = 0;}
        if (Request('reply_reminders')) {$data['reply_reminders'] = 0;}
        if (Request('upvote_reminders')) {$data['upvote_reminders'] = 0;}
        if (Request('reward_reminders')) {$data['reward_reminders'] = 0;}
        if (Request('administration_reminders')) {$data['administration_reminders'] = 0;}
        if (Request('default_collection_updates')) {$data['default_collection_updates'] = 0;}
        if (Request('public_notice_id')) {$data['public_notice_id'] = Request('public_notice_id');}

        $info->update($data);
        return response()->success(new UserReminderResource($info));
    }

    public function getPreference($id,Request $request)
    {
        if (!auth('api')->user()->isAdmin() && auth('api')->id() != $id) {abort(403, '不可查看该用户设置');}

        $user = CacheUser::user($id);
        $info = CacheUser::info($id);
        if (!$user || !$info) {abort('404', '用户不存在');}

        return response()->success(new UserPreferenceResource($info));
    }

    public function updatePreference($id,Request $request)
    {
        if (auth('api')->id() != $id) {abort(403, '不可修改该用户设置');}
        $info = CacheUser::info($id);

        $data = [];
        $data['no_upvote_reminders'] = $request->no_upvote_reminders? true:false;
        $data['no_reward_reminders'] = $request->no_reward_reminders? true:false;
        $data['no_message_reminders'] = $request->no_message_reminders? true:false;
        $data['no_reply_reminders'] = $request->no_reply_reminders? true:false;
        $data['no_stranger_msg'] = $request->no_stranger_msg? true:false;

        if ($list_id = $this->get_id(Request('default_list_id'), 'findLists', $id)) {
            $data['default_list_id'] = $list_id;
        }
        if ($box_id = $this->get_id(Request('default_box_id'), 'findBoxes', $id)) {
            $data['default_box_id'] = $box_id;
        }
        if ($group_id = $this->get_id(Request('default_collection_group_id'), 'findCollectionGroups', $id)) {
            $data['default_collection_group_id'] = $group_id;
        }

        $info->update($data);
        return response()->success(new UserPreferenceResource($info));
    }

    public function updateIntro($id, Request $request)
    {
        if(!auth('api')->user()->isAdmin()&&($id!=auth('api')->id())){abort(403, '不可修改该用户介绍');}

        $validator = Validator::make($request->all(), [
            'brief_intro' => 'required|string|max:50',
            'body' => 'required|string|max:2000',
        ]);
        if ($validator->fails()) {
            return response()->error($validator->errors(), 422);
        }

        $info = CacheUser::info($id);
        $info->update([
            'has_intro' => 1,
            'brief_intro' => request('brief_intro'),
        ]);
        $intro = UserIntro::updateOrCreate([
            'user_id' => $id,
        ],[
            'body' => request('body'),
            'edited_at' => Carbon::now(),
        ]);

        return response()->success(new UserIntroResource($intro));
    }

    public function showThread($id, Request $request)
    {
        $threads = $this->get_threads_or_books(0, $id, $request);

        return response()->success(ThreadBriefResource::collection($threads));
    }

    public function showBook($id, Request $request)
    {
        $books = $this->get_threads_or_books(1, $id, $request);

        return response()->success(ThreadBriefResource::collection($books));
    }

    public function showPost($id, Request $request)
    {
        // 管理员或本人可查看包括匿名、边缘、折叠以及发表在非公开thread、非公开板块内thread的post
        if(auth('api')->user()->isAdmin() || auth('api')->id() == $id){
            $posts = $this->select_user_comments(1, $id,$request);
        }elseif(auth('api')->user()->level > 0){
            $posts = $this->select_user_comments(0, $id,$request);
        } else {
            abort(403);
        }

        return response()->success([
            'posts' => PostResource::collection($posts),
            'paginate' => new PaginateResource($posts),
        ]);
    }

    public function showStatus($id,Request $request)
    {
        if(auth('api')->check() && (auth('api')->user()->isAdmin() || auth('api')->id() == $id)){
            $statuses = Status::with('author.title')
            ->withUser($id)
            ->ordered()
            ->paginate(config('preference.statuses_per_page'));
        } else {
            $queryid = 'UserStatus.'
            .$id
            .(is_numeric($request->page)? 'P'.$request->page:'P1');

            $statuses = Cache::remember($queryid, 10, function () use($request, $id) {
                return Status::with('author.title')
                ->withUser($id)
                ->isPublic()
                ->ordered()
                ->paginate(config('preference.statuses_per_page'))
                ->appends($request->only('page'));
            });

        }

        return response()->success([
            'statuses' => StatusResource::collection($statuses),
            'paginate' => new PaginateResource($statuses),
        ]);
    }

    private function get_threads_or_books($is_book, $id, $request) {

        // 管理员及本人可查看用户所有thread或book，其他只能查看公开非匿名（包括边缘）
        if (auth('api')->check() && (auth('api')->user()->isAdmin() || auth('api')->id() == $id)) {
            $data = $this->select_user_threads(1, $is_book, $id, $request);
        } else {
            $data = $this->select_user_threads(0, $is_book, $id, $request);
        }

        return $data;
    }

    private function get_id($default_id, $method, $user_id) {
        if($default_id){
            $id = (int)$default_id;
            $ids = $this->$method($user_id)->pluck('id')->toArray();
            if(in_array($id, $ids)){
                return $id;
            }
        }
        return;
    }
}
