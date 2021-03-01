<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\ConstantObjects;
class Firewall
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
        $restricted_ips = ConstantObjects::firewall_ips();
        if (!empty($restricted_ips)&&(in_array(request()->ip(), $restricted_ips)))
        {
            \Log::warning("Unauthorized access, IP address was => ".request()->ip());
            abort(499);
        }
        return $next($request);
    }
}
