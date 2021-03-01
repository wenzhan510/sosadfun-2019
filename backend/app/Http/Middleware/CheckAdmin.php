<?php
namespace App\Http\Middleware;
use Closure;
use Auth;
class CheckAdmin
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
            abort(401, '用户未登录');
        }
        if(!auth('api')->user()->isAdmin()){
            abort(403, '需要管理员权限');
        }
        return $next($request);
    }
}
