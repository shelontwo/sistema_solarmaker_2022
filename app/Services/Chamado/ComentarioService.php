<?php

namespace App\Services\Chamado;

use Exception;
use App\Models\Chamado;
use App\Models\Usuario;
use App\Helpers\HelperBuscaId;
use App\Models\ChamadoComentario;
use Illuminate\Support\Facades\Validator;

class ComentarioService
{
    protected $data;

    public function defineData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function indice($request, $uuidChamado)
    {
        try {
            $fk_cha_id_chamado = HelperBuscaId::buscaId($uuidChamado, Chamado::class);
            
            $comentarios = ChamadoComentario::select('uuid_cco_id', 'cco_id', 'cco_comentario', 'cco_criado_em', 'fk_usu_id_usuario')
                ->where('fk_cha_id_chamado', $fk_cha_id_chamado)
                ->with('usuario');
            $comentarios = $request->input('page') ? $comentarios->paginate() : $comentarios->get();
            
            return ['status' => true, 'data' => $comentarios];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listComentario($uuidChamado, $uuidProd)
    {
        try {
            $fk_cha_id_chamado = HelperBuscaId::buscaId($uuidChamado, Chamado::class);
            $comentario = ChamadoComentario::where('fk_cha_id_chamado', $fk_cha_id_chamado)->where('uuid_cco_id', $uuidProd)->first();
            return ['status' => true, 'data' => $comentario];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function novoComentario($uuidChamado)
    {
        try {
            $this->data['uuid_cha_id'] = $uuidChamado;
            $this->data['fk_usu_id_usuario'] = auth()->user()->usu_id;

            $validacao = $this->validaCampos();
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_cha_id_chamado'] = HelperBuscaId::buscaId($this->data['uuid_cha_id'], Chamado::class);
            
            $comentario = ChamadoComentario::create($this->data);

            return ['status' => true, 'data' => $comentario];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function atualizaComentario($uuidChamado)
    {
        try {
            $this->data['uuid_cha_id'] = $uuidChamado;
            $this->data['fk_usu_id_usuario'] = auth()->user()->usu_id;

            $validacao = $this->validaCampos(true);
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_cha_id_chamado'] = HelperBuscaId::buscaId($this->data['uuid_cha_id'], Chamado::class);

            $comentario = ChamadoComentario::find(HelperBuscaId::buscaId($this->data['uuid_cco_id'], ChamadoComentario::class));
            $comentario->update($this->data);

            return ['status' => true, 'data' => $comentario];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function removeComentario($uuidChamado, $uuidProd)
    {
        try {
            $fk_cha_id_chamado = HelperBuscaId::buscaId($uuidChamado, Chamado::class);
            $comentario = ChamadoComentario::where('fk_cha_id_chamado', $fk_cha_id_chamado)->where('uuid_cco_id', $uuidProd)->delete();
            return ['status' => true, 'msg' => $comentario ? 'Comentário removido com sucesso' : 'Erro ao remover comentário'];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    private function validaCampos($update = false)
    {
        $validacao = [
            'cco_comentario' => 'required|string',
            'fk_usu_id_usuario' => 'required|integer',
            'uuid_cha_id' => 'required|uuid',
        ];

        if ($update) {
            $validacao['uuid_cco_id'] = 'required|uuid';
        }

        return Validator::make($this->data, $validacao);
    }
}