<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Follower;
use App\Http\Resources\UserBriefResource;
use App\Http\Resources\UserFollowResource;
use App\Http\Resources\PaginateResource;
use Validator;
use CacheUser;

use DB;

class FollowerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['follower','following']);
    }

    /**
    * follow 关注某人
    */
    public function store($id)
    {
        $user = CacheUser::user($id);
        if(!$user){abort(404);}

        if (auth('api')->id()===$user->id){abort(411);}

        $follow_relationship = \App\Models\Follower::where('user_id', $id)->where('follower_id', auth('api')->id())
        ->first();

        if ($follow_relationship){abort(409);}

        auth('api')->user()->follow($user->id);

        return response()->success([
            'user' => new UserBriefResource($user),
        ]);

    }

    /**
    * unfollow
    */
    public function destroy($id)
    {
        $user = CacheUser::user($id);

        if (auth('api')->id()===$user->id){abort(411, '不能取关自己');}

        $follow_relationship = \App\Models\Follower::where('user_id', $id)->where('follower_id', auth('api')->id())
        ->first();

        if (!$follow_relationship){abort(412,'未关注');}

        $follow_relationship->delete();

        return response()->success([
            'user' => new UserBriefResource($user),
        ]);

    }

    /**
    * switch whether to receive updates of this user
    */
    public function update($id, Request $request)
    {
        $user = CacheUser::user($id);

        if(!$user){abort(404);}

        $follow_relationship = \App\Models\Follower::where('user_id', $id)->where('follower_id', auth('api')->id())
        ->first();

        if(!$follow_relationship){abort(412);}

        $validator = Validator::make($request->all(), [
            'keep_updated' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return response()->error($validator->errors(), 422);
        }

        $follow_relationship->update(['keep_updated'=>$request->keep_updated]);

        return response()->success(new UserFollowResource($follow_relationship));
    }

    /**
    * show the profile of the relationship for the given following
    **/
    //
    public function show($id)
    {
        $user = CacheUser::user($id);

        if(!$user){abort(404);}

        $follow_relationship = \App\Models\Follower::where('user_id', $id)->where('follower_id', auth('api')->id())
        ->first();

        if(!$follow_relationship){abort(404);}

        $follow_relationship->load('user_brief');

        return response()->success(new UserFollowResource($follow_relationship));

    }

    /**
    * 好友关系
    **/
    public function follower($id)
    {
        $user = CacheUser::user($id);
        if(!$user){abort(404);}

        $followers = $user->followers()->paginate(config('constants.index_per_page'));

        return response()->success([
            'user'=> new UserBriefResource($user),
            'followers' => UserBriefResource::collection($followers),
            'paginate' => new PaginateResource($followers),
        ]);
    }

    public function following($id)
    {
        $user = CacheUser::user($id);
        if(!$user){abort(404);}

        $followings = $user->followings()->paginate(config('constants.index_per_page'));

        return response()->success([
            'user'=> new UserBriefResource($user),
            'followings' => UserBriefResource::collection($followings),
            'paginate' => new PaginateResource($followings),
        ]);
    }

    public function followingStatuses($id)
    {
        $user = CacheUser::user($id);
        if(!$user){abort(404);}

        if(auth('api')->id()!=$user->id){abort(403);}

        $follow_relationships = \App\Models\Follower::with('user_brief')->where('follower_id', auth('api')->id())->paginate(config('constants.index_per_page'));

        return response()->success([
            'user'=> new UserBriefResource($user),
            'followingStatuses' => UserFollowResource::collection($follow_relationships),
            'paginate' => new PaginateResource($follow_relationships),
        ]);
    }

}
