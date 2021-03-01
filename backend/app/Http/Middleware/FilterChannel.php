<?php

namespace App\Http\Middleware;
use Closure;

class FilterChannel
{
    /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
    public function handle($request, Closure $next)
    {
        $channel = collect(config('channel'))->keyby('id')->get($request->route('channel'));
        if(!$channel){
            abort(404);
        }
        if(!$channel->is_public&&!auth('api')->check()){
            abort(401);
        }

        if($channel->type=='homework'&&!auth('api')->user()->isAdmin()){
            abort(403);
        }

        if(!$channel->is_public&&auth('api')->check()&&!auth('api')->user()->canSeeChannel($channel->id)){
            abort(403);
        }
        return $next($request);
    }
}
