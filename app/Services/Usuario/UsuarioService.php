<?php

namespace App\Services\Usuario;

use Exception;
use App\Models\Grupo;
use App\Models\Usuario;
use App\Models\Integrador;
use App\Models\Distribuidor;
use App\Helpers\HelperBuscaId;
use App\Services\S3\S3Service;
use Illuminate\Support\Facades\Hash;
use App\Helpers\HelperVerificaDiferenca;
use Illuminate\Support\Facades\Validator;

class UsuarioService
{
    protected $data;

    protected $s3Service;
    
    public function __construct(S3Service $s3Service)
    {
        $this->s3Service = $s3Service;
    }

    public function defineData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function indice($request)
    {
        try {
            $usuarios = Usuario::select('uuid_usu_id', 'usu_id', 'usu_nome', 'usu_apelido', 'usu_email', 'fk_gru_id_grupo')->with('grupo');
            $usuarios = $request->input('page') ? $usuarios->paginate() : $usuarios->get();
                
            return ['status' => true, 'data' => $usuarios];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listUsuariosMaster($request)
    {
        try {
            $usuarios = Usuario::select('uuid_usu_id', 'usu_id', 'usu_nome', 'usu_apelido', 'usu_email', 'fk_gru_id_grupo')
                ->whereNull('fk_dis_id_distribuidor')
                ->whereNull('fk_int_id_integrador')
                ->with('grupo');
            $usuarios = $request->input('page') ? $usuarios->paginate() : $usuarios->get();
                
            return ['status' => true, 'data' => $usuarios];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listUsuariosDistribuidor($request, $uuid)
    {
        try {
            $fk_dis_id_distribuidor = HelperBuscaId::buscaId($uuid, Distribuidor::class);
            $usuarios = Usuario::select('uuid_usu_id', 'usu_id', 'usu_nome', 'usu_apelido', 'usu_email', 'fk_gru_id_grupo')
                ->where('fk_dis_id_distribuidor', $fk_dis_id_distribuidor)
                ->with('grupo');
            $usuarios = $request->input('page') ? $usuarios->paginate() : $usuarios->get();
                
            return ['status' => true, 'data' => $usuarios];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listUsuariosIntegrador($request, $uuid)
    {
        try {
            $fk_int_id_integrador = HelperBuscaId::buscaId($uuid, Integrador::class);
            $usuarios = Usuario::select('uuid_usu_id', 'usu_id', 'usu_nome', 'usu_apelido', 'usu_email', 'fk_gru_id_grupo')
                ->where('fk_int_id_integrador', $fk_int_id_integrador)
                ->with('grupo');
            $usuarios = $request->input('page') ? $usuarios->paginate() : $usuarios->get();
                
            return ['status' => true, 'data' => $usuarios];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listUsuario($uuid)
    {
        try {
            $usuario = Usuario::where('uuid_usu_id', $uuid)
                ->with('grupo', 'distribuidor', 'integrador')
                ->first();
            return ['status' => true, 'data' => $usuario];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function novoUsuario()
    {
        try {
            $validacao = $this->validaCampos();
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_gru_id_grupo'] = HelperBuscaId::buscaId($this->data['uuid_gru_id'], Grupo::class);
            unset($this->data['uuid_gru_id']);
            
            if (!empty($this->data['uuid_dis_id'])) {
                $this->data['fk_dis_id_distribuidor'] = HelperBuscaId::buscaId($this->data['uuid_dis_id'], Distribuidor::class);
                unset($this->data['uuid_dis_id']);
            } else if (!empty($this->data['uuid_int_id'])) {
                $this->data['fk_int_id_integrador'] = HelperBuscaId::buscaId($this->data['uuid_int_id'], Integrador::class);
                unset($this->data['uuid_int_id']);
            }

            $this->data['password'] = Hash::make($this->data['password']);

            if (isset($this->data['usu_imagem']) && $this->data['usu_imagem'] != 'null') {
                $this->data['usu_imagem'] = $this->s3Service->enviaS3($this->data['usu_imagem'], 'usuarios')->get('ObjectURL');
            }

            $usuario = Usuario::create($this->data);

            return ['status' => true, 'data' => $usuario];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function atualizaUsuario()
    {
        try {
            $usuarioId = HelperBuscaId::buscaId($this->data['uuid_usu_id'], Usuario::class);
            $dadosAlterar = HelperVerificaDiferenca::verificaDiferenca($usuarioId, $this->data, Usuario::class);
            $this->defineData($dadosAlterar);

            $validacao = $this->validaCampos(true);
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_gru_id_grupo'] = HelperBuscaId::buscaId($this->data['uuid_gru_id'], Grupo::class);
            if (!empty($this->data['password'])) {
                $this->data['password'] = Hash::make($this->data['password']);
            }
            unset($this->data['uuid_gru_id']);

            if (isset($this->data['usu_imagem']) && $this->data['usu_imagem'] != 'null') {
                $this->data['usu_imagem'] = $this->s3Service->enviaS3($this->data['usu_imagem'], 'usuarios')->get('ObjectURL');
            }
            
            $usuario = Usuario::find($usuarioId);
            $usuario->update($this->data);

            return ['status' => true, 'data' => $usuario];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function removeUsuario($uuid)
    {
        try {
            $usuario = Usuario::where('uuid_usu_id', $uuid)->delete();
            return ['status' => true, 'msg' => $usuario ? 'Usuário removido com sucesso' : 'Erro ao remover usuário'];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    private function validaCampos($update = false)
    {
        $validacao = [
            'usu_apelido' => 'required|string|max:255|unique:usuarios',
            'usu_nome' => 'required|string|max:255',
            'usu_email' => 'string|email|max:255|unique:usuarios|nullable',
            'password' => 'required|string|min:6',
            'usu_data_referencia' => 'date|nullable',
            'usu_dias_expiracao' => 'integer|nullable',
            'uuid_gru_id' => 'required|string|uuid',
            'uuid_int_id' => 'uuid|nullable',
            'uuid_dis_id' => 'uuid|nullable',
        ];

        if ($update) {
            $validacao = [
                'usu_apelido' => 'string|max:255|unique:usuarios',
                'usu_nome' => 'string|max:255',
                'usu_email' => 'string|email|max:255|unique:usuarios',
                'password' => 'string|min:6',
                'usu_data_referencia' => 'date|nullable',
                'usu_dias_expiracao' => 'integer|nullable',
                'uuid_gru_id' => 'required|uuid',
            ];
        }

        return Validator::make($this->data, $validacao);
    }
}