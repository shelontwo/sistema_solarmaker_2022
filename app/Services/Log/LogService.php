<?php

namespace App\Services\Log;

use Exception;
use App\Models\Log;

class LogService
{
    protected $data;

    public function defineData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function indice($request)
    {
        try {
            $logs = Log::select('uuid_log_id', 'fk_usu_id_usuario', 'log_url', 'log_method', 'log_status')->paginate();
            return ['status' => true, 'data' => $logs];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listaLog($uuid)
    {
        try {
            $log = Log::where('uuid_log_id', $uuid)->first();
            return ['status' => true, 'data' => $log];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }
}