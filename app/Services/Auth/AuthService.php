<?php

namespace App\Services\Auth;

use Exception;
use App\Models\Grupo;
use App\Models\Usuario;
use App\Helpers\HelperBuscaId;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthService
{
    protected $data;

    public function defineData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function novoUsuario()
    {
        try {
            $fk_gru_id_grupo = HelperBuscaId::buscaId($this->data['uuid_gru_id'], Grupo::class);

            $user = Usuario::create([
                'usu_nome' => $this->data['usu_nome'],
                'usu_apelido' => $this->data['usu_apelido'],
                'usu_email' => $this->data['usu_email'],
                'usu_tipo' => $this->data['usu_tipo'],
                'password' => Hash::make($this->data['password']),
                'fk_gru_id_grupo' => $fk_gru_id_grupo,
            ]);

            return ['status' => true, 'data' => $user];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    
}