<?php
namespace App\Sosadfun\Traits;

use Cache;

trait CollectionObjectTraits{

    public function findCollectionGroups($user_id)
    {
        return Cache::remember('collectionGroups.'.$user_id, 10, function () use($user_id){
            return \App\Models\CollectionGroup::where('user_id',$user_id)->get();
        });
    }

    public function clearCollectionGroups($user_id)
    {
        Cache::forget('collectionGroups.'.$user_id);
    }

    public function findCollectionIndex($user_id)
    {
        $collections = \App\Models\Collection::where('user_id', $user_id)
        ->get();
        $collections->load('briefThread.author','briefThread.tags','briefThread.last_post','briefThread.last_component');
        return $collections;
    }

    public function clearCollectionGroupUpdateCount($user_id)
    {
        \App\Models\CollectionGroup::where('user_id',$user_id)->where('update_count','>',0)->update(['update_count'=>0]);
    }
}
