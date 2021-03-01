<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ConstantObjects;

use App\Http\Resources\CollectionResource;
use App\Http\Resources\PaginateResource;

use App\Models\Collection;
use CacheUser;
use Cache;

use App\Sosadfun\Traits\CollectionObjectTraits;
use App\Sosadfun\Traits\FindThreadTrait;

class CollectionController extends Controller
{

    use CollectionObjectTraits;
    use FindThreadTrait;

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('filter_thread')->only('store');
    }

    public function index($id, Request $request)//显示自己的收藏内容
    {
        $user = CacheUser::user($id);
        $info = CacheUser::info($id);
        if(!$user){abort(404);}
        if(!auth('api')->user()->isAdmin()&&($user->id!=auth('api')->id())){abort(403,'只能看自己的收藏');}

        $collections = $this->findCollectionIndex($user->id);

        return response()->success([
            'collections' => CollectionResource::collection($collections),
        ]);

    }
    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store($thread, Request $request)//
    {

        if(Cache::has('AddToCollectionByUser.'.auth('api')->id())){abort(410,'2分钟内只能收藏一本图书');}

        $thread = $this->findThread($thread);
        if(!$thread){abort(404);}

        if(auth('api')->user()->isCollecting($thread->id)){abort(409,'已收藏');}

        if($thread->deletion_applied_at){abort(413,'本文申请删除中，无法被收藏!');}

        $group_id = 0;
        if($request->group_id>0){
            $groups = $this->findCollectionGroups(auth()->id());
            $group = $groups->keyby('id')->get($request->group_id);
            $group_id = $group? $group->id:0;
        }

        $collection = Collection::create([
            'user_id' => auth('api')->id(),
            'thread_id' => $thread->id,
            'keep_updated' =>1,
            'updated' => false,
            'group_id' => $group_id,
            'last_read_post_id' => 0,
        ]);

        Cache::put('AddToCollectionByUser.'.auth('api')->id(),1,2);
        $thread->increment('collection_count');

        return response()->success(new CollectionResource($collection));
    }


    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\Models\Collection  $collection
    * @return \Illuminate\Http\Response
    */

    public function update(Collection $collection, Request $request)
    {
        if($collection->user_id!=auth('api')->id()){abort(403);}

        $this->validate($request, [
            'keep_updated' => 'boolean',
            'group_id' => 'numeric',
            'last_read_post_id' => 'numeric',
        ]);

        $collection_data = $request->only('keep_updated','group_id','last_read_post_id');

        if($request->group_id){
            $groups = $this->findCollectionGroups(auth('api')->id());
            $newgroup = $groups->keyby('id')->get($request->group_id);
            if(!$newgroup||$newgroup->user_id!=auth('api')->id()){abort(422, '收藏分页信息错误');}
        }

        $collection_data['updated']=0;

        $collection->update($collection_data);

        return response()->success(new CollectionResource($collection));
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Collection  $collection
    * @return \Illuminate\Http\Response
    */
    public function destroy(Collection $collection)
    {
        if($collection->user_id!=auth('api')->id()){abort(403);}

        $thread_id = $collection->thread_id;
        $thread = $this->findThread($thread_id);
        if($thread){
            $thread->decrement('collection_count');
        }
        $collection->delete();
        return response()->success('deleted this collection');
    }

    public function clear_updates($id)
    {
        DB::table('collections')->join('threads','collections.thread_id','=','threads.id')
        ->where('collections.user_id',$id)
        ->where('threads.last_component_id','>',0)
        ->update(['collections.last_read_post_id' => 'threads.last_component_id']);
        return response()->success('cleared collection updates');
    }

}
