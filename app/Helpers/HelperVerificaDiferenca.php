<?php

namespace App\Helpers;

Use Exception;

class HelperVerificaDiferenca
{
    public static function verificaDiferenca($id, $alterado, $model)
    {
        try {
            $atual = $model::find($id)->toArray();
            
            return array_diff($alterado, $atual);
        } catch(\Exception $error) {
            throw new Exception('Erro ao comparar dados', 1);
        }
    }
}