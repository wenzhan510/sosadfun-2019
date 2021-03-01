<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityResource;
use App\Http\Resources\PaginateResource;
use DB;
use ConstantObjects;
use CacheUser;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index($id)
    {
        $user = CacheUser::user($id);
        if(!$user){abort(404);}

        if((auth('api')->id()!= $id)&&(!auth('api')->user()->isAdmin())){abort(403);}

        $activities = \App\Models\Activity::with('item.author')
        ->withUser($id)
        ->orderBy('id', 'desc')
        ->paginate(config('preference.messages_per_page'));

        $activities->loadMorph('item', [
            \App\Models\Post::class => ['simpleThread'],
        ]);

        return response()->success([
            'activities' => ActivityResource::collection($activities),
            'paginate' => new PaginateResource($activities),
        ]);

    }

    public function clearupdates(Request $request)
    // TODO 未来允许用户已读具体某条提醒或消息
    {
        $user = auth('api')->user();
        $info = $user->info;

        if(!$user||!$info){abort(404);}

        DB::transaction(function()use($user, $info){
            DB::table('activities')
            ->where('user_id',$user->id)
            ->where('seen',0)
            ->update([
                'seen' =>1,
            ]);

            DB::table('messages')
            ->where('receiver_id',$user->id)
            ->where('seen',0)
            ->update([
                'seen' =>1,
            ]);

            $info->message_reminders=0;
            $info->reward_reminders=0;
            $info->upvote_reminders=0;
            $info->reply_reminders=0;
            $info->administration_reminders=0;
            $info->public_notice_id=ConstantObjects::system_variable()->latest_public_notice_id;
            $info->unread_reminders=0;
            $info->unread_updates=0;
            $info->save();
        });

        return response()->success();
    }

}
