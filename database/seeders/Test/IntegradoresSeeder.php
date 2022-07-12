<?php

namespace Database\Seeders\Test;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class IntegradoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('integradores')->insert([
            'uuid_int_id' => Str::uuid()->toString(),
            'int_nome' => 'Integrador - D3T',
            'int_nome_fantasia' => 'Integrador fantasia',
            'int_cnpj' => '63.893.276/0001-66',
            'int_cep' => '89700-000',
            'int_uf' => 'SC',
            'int_cidade' => 'Concórdia',
            'int_bairro' => 'Centro',
            'int_rua' => 'Rua do centro',
            'int_numero' => '321',
            'int_complemento' => 'Sala 01',
            'int_telefone' => '(49) 3799-0363',
            'int_celular' => '(49) 99192-7778',
            'int_email' => 'integrador@d3t.com.br',
            'fk_dis_id_distribuidor' => 1,
        ]);
    }
}
