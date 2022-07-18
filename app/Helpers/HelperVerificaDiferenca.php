<?php

namespace App\Helpers;

use Exception;

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