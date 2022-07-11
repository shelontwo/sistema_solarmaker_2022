<?php

namespace App\Helpers;

Use Exception;

class HelperBuscaId
{
    public static function buscaId($uuid, $model)
    {
        $data = $model::where($model::UUID, $uuid)->first();
        
        if ($data) {
            return $data->getKey();
        }
        throw new Exception('UUID não válido', 1);
    }
}