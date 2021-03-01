<?php
namespace App\Sosadfun\Traits;

use Cache;

trait StatusObjectTraits{

    public function statusProfile($id)
    {
        return Cache::remember('statusProfile.'.$id, 5, function () use($id) {
            $status = \App\Models\Status::find($id);
            if(!$status){
                return;
            }
            $status->load('author.title','replies.last_reply','replies.author.title','parent','attachable');

            $status->setAttribute('recent_rewards', $status->latest_rewards());
            $status->setAttribute('recent_upvotes', $status->latest_upvotes());

            return $status;
        });
    }
    public function clearStatus($id)
    {
        return Cache::forget('statusProfile.'.$id);
    }

}
