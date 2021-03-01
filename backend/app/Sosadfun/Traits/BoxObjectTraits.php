<?php
namespace App\Sosadfun\Traits;

use DB;
use Cache;
use ConstantObjects;

trait BoxObjectTraits{

    public function findBoxes($id)
    {
        return Cache::remember('Boxes.'.$id, 15, function () use($id){
            return \App\Models\Thread::withUser($id)->WithType('box')->get();
        });
    }

    public function refreshBoxes($id)
    {
        Cache::forget('Boxes.'.$id);
    }
}
