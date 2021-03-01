<?php
namespace App\Sosadfun\Traits;

use DB;
use Cache;
use ConstantObjects;

trait MessageObjectTraits{

    public function findAllPulicNotices(){
        return Cache::remember('publicNotices', 15, function () {
            return \App\Models\PublicNotice::with('author')
            ->orderBy('created_at','desc')
            ->get();
        });
    }
    public function refreshPulicNotices(){
        return Cache::forget('publicNotices');
    }
}
