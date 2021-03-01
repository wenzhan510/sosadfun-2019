<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\CollectionGroup;
use App\Http\Controllers\Controller;
use DB;
use CacheUser;
use App\Http\Resources\CollectionGroupResource;
use App\Sosadfun\Traits\CollectionObjectTraits;


class CollectionGroupController extends Controller
{
    use CollectionObjectTraits;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index($id)
    {
        $user = CacheUser::user($id);
        if(!$user){abort(404);}
        if(!auth('api')->user()->isAdmin()&&($user->id!=auth('api')->id())){abort(403,'只能看自己的收藏');}

        $collection_groups = $this->findCollectionGroups($user->id);

        return response()->success([
             'collection_groups' => CollectionGroupResource::collection($collection_groups),
        ]);
    }

    public function store(Request $request)
    {
        $user = auth('api')->user();

        if(!$user){abort(403);}

        $this->validate($request, [
            'name' => 'required|string|max:10',
            'order_by' => 'required|numeric|min:0|max:4'
        ]);

        $groups = $user->collection_groups;

        if($groups&&$groups->count()>=$user->level){abort(410,'等级不足，不能建立更多收藏分页');}

        $collection_group = CollectionGroup::create([
            'name' => request('name'),
            'user_id' => $user->id,
            'order_by' => request('order_by'),
        ]);

        if(request()->set_as_default_group){
            $user->info->update([
                'default_collection_group_id' => $collection_group->id,
            ]);
        }

        $this->clearCollectionGroups($user->id);
        $collection_groups = $this->findCollectionGroups($user->id);

        return response()->success([
             'collection_groups' => CollectionGroupResource::collection($collection_groups),
        ]);
    }

    public function update(CollectionGroup $collection_group, Request $request)
    {

        $user = auth('api')->user();

        if($collection_group->user_id!=$user->id){abort(403, '必须本人才能修改自己的收藏页');}

        $this->validate($request, [
            'name' => 'required|string|max:10',
            'order_by' => 'required|numeric|min:0|max:4'
        ]);

        $group_data = $request->only('name','order_by');
        $group_data['update_count'] = 0;

        $collection_group->update($group_data);

        if(request()->set_as_default_group){
            $user->check_default_collection_group($collection_group->id, true);
        }

        $this->clearCollectionGroups($user->id);
        $collection_groups = $this->findCollectionGroups($user->id);

        return response()->success([
             'collection_groups' => CollectionGroupResource::collection($collection_groups),
        ]);
    }

    public function destroy(CollectionGroup $collection_group)
    {
        $user = auth('api')->user();

        if($collection_group->user_id!=$user->id){abort(403, '必须本人才能删除自己的收藏页');}

        $collections = DB::table('collections')
        ->join('threads', 'threads.id', '=', 'collections.thread_id')
        ->where('collections.user_id', auth('api')->id())
        ->where('collections.group_id',$collection_group->id)
        ->update(['collections.group_id'=>0]);

        $user->check_default_collection_group($collection_group->id, false);

        $collection_group->delete();

        $this->clearCollectionGroups($user->id);
        $collection_groups = $this->findCollectionGroups($user->id);

        return response()->success([
             'collection_groups' => CollectionGroupResource::collection($collection_groups),
        ]);
    }


}
