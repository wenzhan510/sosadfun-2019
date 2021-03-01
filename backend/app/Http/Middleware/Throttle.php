<?php
namespace App\Http\Middleware;
use Closure;
use Auth;
use Log;
use Illuminate\Routing\Middleware\ThrottleRequests;
class Throttle extends ThrottleRequests
{

  protected function resolveRequestSignature($request)
  {
      // throttle on userid if available, for visitor, throttle on ip
      if ($userID = auth('api')->id()) {
          return sha1($userID);
      }

      if ($route = $request->route()) {
          return sha1($route->getDomain().'|'.$request->ip());
      }

      throw new RuntimeException('Unable to generate the request signature. Route unavailable.');
  }

      /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int|string  $maxAttempts
     * @param  float|int  $decayMinutes
     * @param  string  $prefix
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Http\Exceptions\ThrottleRequestsException
     */
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1, $prefix = '')
    {
        $key = $prefix.$this->resolveRequestSignature($request);

        $maxAttempts = $this->resolveMaxAttempts($request, $maxAttempts);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = $this->getTimeUntilNextRetry($key);
            $response = response()->error('Too Many Attempts.', 429);
            return $this->addHeaders(
                $response,
                $maxAttempts,
                $this->calculateRemainingAttempts($key, $maxAttempts, $retryAfter),
                $retryAfter
            );
        }
        $this->limiter->hit($key, $decayMinutes);

        $response = $next($request);

        if (!$response->headers->get('X-RateLimit-Limit')){
          $this->addHeaders(
            $response, $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
        }
        
        return $response;
    }
}
