<?php
namespace App\Http\Middleware;
use Closure;
use Auth;
use Cache;
use Carbon;
use CacheUser;

class NoLogControl
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
        if(auth('api')->check()) {
            $user = auth('api')->user();
            if($user->no_logging){
                $info = CacheUser::info(auth('api')->id());
                $msg = $info->no_logging_until&&$info->no_logging_until>Carbon::now() ? $info->no_logging_until->setTimeZone('Asia/Shanghai'):'';

                $userTokens = $user->tokens;
                foreach($userTokens as $token) {
                    $token->revoke();
                }
                auth('api')->logout();
                abort(495,$msg);
            }
        }
        return $next($request);
    }
}
