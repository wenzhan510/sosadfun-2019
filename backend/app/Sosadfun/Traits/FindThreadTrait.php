<?php
namespace App\Sosadfun\Traits;

use Cache;

trait FindThreadTrait{

    public function findThread($id)
    {
        return Cache::remember('thread.'.$id, 10, function () use($id){
            $thread = \App\Models\Thread::find($id);
            if($thread){
                $thread->load('tags', 'author.title');
            }
            return $thread;
        });
    }
}
