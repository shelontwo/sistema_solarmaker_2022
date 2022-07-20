<?php

namespace App\Services\Usina;

use Exception;
use App\Models\Usina;
use App\Models\UsinaStatus;
use App\Models\UsinaProjeto;
use App\Helpers\HelperBuscaId;
use Illuminate\Support\Facades\Validator;

class ProjetoService
{
    protected $data;

    public function defineData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function listProjeto($uuidUsina)
    {
        try {
            $fk_usi_id_usina = HelperBuscaId::buscaId($uuidUsina, Usina::class);
            $projeto = UsinaProjeto::where('fk_usi_id_usina', $fk_usi_id_usina)->with('usina.status')->first();
            return ['status' => true, 'data' => $projeto];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function atualizaProjeto($uuidUsina)
    {
        try {
            $this->data['uuid_usi_id'] = $uuidUsina;
            $validacao = $this->validaCampos(true);
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_usi_id_usina'] = HelperBuscaId::buscaId($this->data['uuid_usi_id'], Usina::class);

            $projeto = UsinaProjeto::where('fk_usi_id_usina', $this->data['fk_usi_id_usina'])->first();

            if ($projeto) {
                $projeto->update($this->data);
            } else {
                $projeto = UsinaProjeto::create($this->data);
            }

            if ($this->data['uuid_uss_id']) {
                $fk_uss_id_status = HelperBuscaId::buscaId($this->data['uuid_uss_id'], UsinaStatus::class);
                $usina = Usina::find($this->data['fk_usi_id_usina']);
                $usina->update(['fk_uss_id_status' => $fk_uss_id_status]);
            }

            return ['status' => true, 'data' => $projeto];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    private function validaCampos()
    {
        $validacao = [
            'usp_potencia' => 'required|string|max:150',
            'usp_eficiencia' => 'required|string|max:150',
            'usp_data_instalacao' => 'required|date',
            'usp_total_investido' => 'required|numeric',
            'usp_tarifa_referencia' => 'required|numeric',
            'usp_rsi_jan' => 'required|string|max:150',
            'usp_rsi_fev' => 'required|string|max:150',
            'usp_rsi_mar' => 'required|string|max:150',
            'usp_rsi_abr' => 'required|string|max:150',
            'usp_rsi_mai' => 'required|string|max:150',
            'usp_rsi_jun' => 'required|string|max:150',
            'usp_rsi_jul' => 'required|string|max:150',
            'usp_rsi_ago' => 'required|string|max:150',
            'usp_rsi_set' => 'required|string|max:150',
            'usp_rsi_out' => 'required|string|max:150',
            'usp_rsi_nov' => 'required|string|max:150',
            'usp_rsi_dez' => 'required|string|max:150',
            'uuid_uss_id' => 'uuid|nullable',
            'uuid_usi_id' => 'required|uuid'
        ];

        return Validator::make($this->data, $validacao);
    }
}