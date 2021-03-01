<?php
namespace App\Http\Middleware;
use Closure;
use Auth;
use Cache;
use Carbon;
use CacheUser;

class NoHomeworkControl
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
        if(!auth('api')->check()) {
            abort(401);
        }
        if(auth('api')->user()->no_homework){
            abort(497);
        }
        return $next($request);
    }
}
