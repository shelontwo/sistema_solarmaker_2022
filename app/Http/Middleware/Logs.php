<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Log;
use App\Models\Webhook;

class Logs
{
    public $user;

    public function handle($request, Closure $next)
    {
        $user = auth()->user();
        $request->usu_id = $user ? $user->usu_id : null;
        return $next($request);
    }

    public function terminate($request, $response)
    {

        if ($request->path() == 'api/logs') {
            return true;
        }

        if ($request->path() == 'api/usuario/login') {
            $user = auth()->user();
            $request->usu_id = $user ? $user->usu_id : null;
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

        $log = Log::create([
            'fk_usu_id_usuario' => $request->usu_id,
            'log_url' => $request->path(),
            'log_method' => $request->method(),
            'log_request_json' => addslashes($request_json),
            'log_response_json' => "" . addslashes($response_json),
            'log_status' => $response->getStatusCode(),
            'log_ip_address' => $request->getClientIps()[0],
        ]);

        if ($request->webhook_id) {
            $webhook = Webhook::find($request->webhook_id);
            $webhook->update([
                'fk_log_id_log' => $log->log_id
            ]);
        }
    }
}