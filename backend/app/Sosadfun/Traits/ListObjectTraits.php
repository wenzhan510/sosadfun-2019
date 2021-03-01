<?php
namespace App\Sosadfun\Traits;

use DB;
use Cache;
use ConstantObjects;

trait ListObjectTraits{

    public function findLists($id)
    {
        return Cache::remember('Lists.'.$id, 15, function () use($id){
            return \App\Models\Thread::withUser($id)->WithType('list')->get();
        });
    }
    public function refreshLists($id)
    {
        Cache::forget('Lists.'.$id);
    }
}
