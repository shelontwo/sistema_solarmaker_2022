<?php

namespace App\Services\UnidadeConsumidora;

use Exception;
use App\Models\Usina;
use App\Helpers\HelperBuscaId;
use App\Services\S3\S3Service;
use App\Models\UnidadeConsumidora;
use App\Models\UnidadeConsumidoraFatura;
use Illuminate\Support\Facades\Validator;
class FaturaService
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

    public function indice($request, $uuidUnidade)
    {
        try {
            $fk_uco_id_unidade = HelperBuscaId::buscaId($uuidUnidade, UnidadeConsumidora::class);
            $fatura = UnidadeConsumidoraFatura::select('*', 'ucf_arquivo as ucf_arquivo_link')->where('fk_uco_id_unidade', $fk_uco_id_unidade);
            $fatura = $request->input('page') ? $fatura->paginate() : $fatura->get();
            
            return ['status' => true, 'data' => $fatura];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listFatura($uuidUnidade, $uuid)
    {
        try {
            $fk_uco_id_unidade = HelperBuscaId::buscaId($uuidUnidade, UnidadeConsumidora::class);
            $fatura = UnidadeConsumidoraFatura::select('*', 'ucf_arquivo as ucf_arquivo_link')->where('fk_uco_id_unidade', $fk_uco_id_unidade)->where('uuid_ucf_id', $uuid)->first();
            return ['status' => true, 'data' => $fatura];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function novaFatura($uuidUnidade)
    {
        try {
            $this->data['uuid_uco_id'] = $uuidUnidade;
            $validacao = $this->validaCampos();
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            if (isset($this->data['ucf_arquivo']) && $this->data['ucf_arquivo'] != 'null') {
                $this->data['ucf_arquivo'] = $this->s3Service->enviaS3($this->data['ucf_arquivo'], 'faturas')->get('ObjectURL');
            }

            $this->data['fk_uco_id_unidade'] = HelperBuscaId::buscaId($this->data['uuid_uco_id'], UnidadeConsumidora::class);
            
            $fatura = UnidadeConsumidoraFatura::create($this->data);

            return ['status' => true, 'data' => $fatura];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function atualizaFatura($uuidUnidade)
    {
        try {
            $this->data['uuid_uco_id'] = $uuidUnidade;
            $validacao = $this->validaCampos(true);
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            if (isset($this->data['remover_arquivo']) && $this->data['remover_arquivo'] == true) {
                $this->data['ucf_nome_arquivo'] = null;
                $this->data['ucf_arquivo'] = null;
            }

            if (isset($this->data['ucf_arquivo']) && $this->data['ucf_arquivo'] != 'null') {
                $this->data['ucf_arquivo'] = $this->s3Service->enviaS3($this->data['ucf_arquivo'], 'faturas')->get('ObjectURL');
            }

            $this->data['fk_uco_id_unidade'] = HelperBuscaId::buscaId($this->data['uuid_uco_id'], UnidadeConsumidora::class);

            $fatura = UnidadeConsumidoraFatura::find(HelperBuscaId::buscaId($this->data['uuid_ucf_id'], UnidadeConsumidoraFatura::class));
            $fatura->update($this->data);

            return ['status' => true, 'data' => $fatura];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function removeFatura($uuidUnidade, $uuid)
    {
        try {
            $fk_uco_id_unidade = HelperBuscaId::buscaId($uuidUnidade, UnidadeConsumidora::class);
            $fatura = UnidadeConsumidoraFatura::where('fk_uco_id_unidade', $fk_uco_id_unidade)->where('uuid_ucf_id', $uuid)->delete();
            return ['status' => true, 'msg' => $fatura ? 'Fatura removido com sucesso' : 'Erro ao remover fatura'];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    private function validaCampos($update = false)
    {
        $validacao = [
            'ucf_valor_faturado' => 'required|numeric',
            'ucf_inicio_ciclo' => 'required|date',
            'ucf_fim_ciclo' => 'required|date',
            'ucf_valor_tarifa' => 'required|numeric',
            'ucf_consumida' => 'required|string|max:50',
            'ucf_faturada' => 'required|string|max:50',
            'ucf_tarifa_compensada' => 'required|numeric',
            'ucf_energia_compensada' => 'required|string|max:50',
            'ucf_energia_injetada' => 'required|string|max:50',
            'ucf_situacao' => 'required|string|max:50',
            'ucf_nome_arquivo' => 'string|max:255|nullable',
            'ucf_arquivo' => 'file|nullable',
            'uuid_uco_id' => 'required|uuid'
        ];

        if ($update) {
            $validacao['uuid_ucf_id'] = 'required|uuid';
        }

        return Validator::make($this->data, $validacao);
    }
}