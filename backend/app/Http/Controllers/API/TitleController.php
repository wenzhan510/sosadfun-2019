<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Title;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\TitleResource;
use App\Http\Resources\UserBriefResource;
use App\Http\Resources\PaginateResource;
use Validator;
use ConstantObjects;
use CacheUser;

class TitleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except('title');
    }

    public function title($id)
    {
        $user = CacheUser::user($id);
        if(!$user){abort(404);}
        if(auth('api')->check()&&(auth('api')->id()===$id||auth('api')->user()->isAdmin())){
            $user_titles = $user->titles;
        }else{
            $user_titles = $user->public_titles;
        }
        return response()->success([
            'user' => new UserBriefResource($user),
            'titles' => TitleResource::collection($user_titles),
        ]);
    }

    public function wear($title)
    {
        $user = auth('api')->user();
        $title_instance = ConstantObjects::find_title_by_id($title);
        if(!$user||!$title_instance){abort(404);}
        if(!is_numeric($title)){abort(422,'名称不合理');}
        if(!$user->hasTitle($title)){abort(412, '你不具有这个头衔');}

        $user->update(['title_id'=>$title]);
        return response()->success([
            'user' => new UserBriefResource($user),
            'title' => new TitleResource($title_instance),
        ]);
    }

    public function redeem_title(Request $request)
    {
        $user = auth('api')->user();
        if($request->redeem_type==="2019winter"){
            $title = ConstantObjects::find_title_by_id(config('constants.task_titles.2019winter'));
            if($title&&$title->check_availability($user->id)){
                $user->titles()->syncWithoutDetaching($title->id);
                return response()->success([
                    'user' => new UserBriefResource($user),
                    'title' => new TitleResource($title),
                ]);
            }
        }
        abort(420);//什么都没做
    }

}
