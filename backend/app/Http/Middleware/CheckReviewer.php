<?php
namespace App\Http\Middleware;
use Closure;
use Auth;
class CheckReviewer
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
        if(!auth('api')->user()->isReviewer()&&!auth('api')->user()->isEditor()&&!auth('api')->user()->isAdmin()){
            abort(403);
        }
        return $next($request);
    }
}
