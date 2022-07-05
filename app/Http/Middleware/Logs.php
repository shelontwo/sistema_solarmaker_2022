<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
class Logs
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
    return $next($request);
  }

  public function terminate($request, $response)
    {
        $request_json = '';
        if (!empty($request->all())) {
            $request_json = $request->all();

            if (!is_string($request_json)) {
                $request_json = json_encode($request_json, JSON_UNESCAPED_UNICODE);
            }
        }

        $response_json = '';
        if (!empty($response->content())) {
            $response_json = $response->content();

            if (!is_string($response_json)) {
                $response_json = json_encode($response_json, JSON_UNESCAPED_UNICODE);
            }
        }
        
        $user = auth()->user();

        Log::create([
            'fk_user_id' => $user ? $user->id : null,
            'url' => $request->path(),
            'method' => $request->method(),
            'request_json' => addslashes($request_json),
            'response_json' => "" . addslashes($response_json),
            'status' => $response->getStatusCode(),
            'ip_address' => $request->getClientIps()[0],
        ]);
    }
}
