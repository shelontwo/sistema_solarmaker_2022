<?php

namespace App\Services\Auth;

use Exception;
use App\Models\Grupo;
use App\Models\Usuario;
use App\Models\Integrador;
use App\Models\Distribuidor;
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
            $this->data['fk_gru_id_grupo'] = HelperBuscaId::buscaId($this->data['uuid_gru_id'], Grupo::class);
            
            if (!empty($this->data['uuid_dis_id'])) {
                $this->data['fk_dis_id_distribuidor'] = HelperBuscaId::buscaId($this->data['uuid_dis_id'], Distribuidor::class);
                unset($this->data['uuid_dis_id']);
            } else if (!empty($this->data['uuid_int_id'])) {
                $this->data['fk_int_id_integrador'] = HelperBuscaId::buscaId($this->data['uuid_int_id'], Integrador::class);
                unset($this->data['uuid_int_id']);
            }

            $this->data['password'] = Hash::make($this->data['password']);
            unset($this->data['uuid_gru_id']);

            $user = Usuario::create($this->data);

            return ['status' => true, 'data' => $user];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }
}