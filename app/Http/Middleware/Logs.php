<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Log;

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
        if ($request->path() == 'api/logs') {
            return true;
        }

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
            'fk_usu_id_usuario' => $user ? $user->usu_id : null,
            'log_url' => $request->path(),
            'log_method' => $request->method(),
            'log_request_json' => addslashes($request_json),
            'log_response_json' => "" . addslashes($response_json),
            'log_status' => $response->getStatusCode(),
            'log_ip_address' => $request->getClientIps()[0],
        ]);
    }
}