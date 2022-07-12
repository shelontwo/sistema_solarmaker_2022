<?php

namespace Database\Seeders\Test;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DistribuidoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('distribuidores')->insert([
            'uuid_dis_id' => Str::uuid()->toString(),
            'dis_nome' => 'Distribuidor - D3T',
            'dis_nome_fantasia' => 'Distribuidor fantasia',
            'dis_cnpj' => '79.464.520/0001-05',
            'dis_cep' => '89700-000',
            'dis_uf' => 'SC',
            'dis_cidade' => 'Concórdia',
            'dis_bairro' => 'Centro',
            'dis_rua' => 'Rua do centro',
            'dis_numero' => '123',
            'dis_complemento' => 'Sala 01',
            'dis_telefone' => '(49) 3578-0472',
            'dis_celular' => '(49) 98642-3936',
            'dis_email' => 'distribuidor@d3t.com.br'
        ]);
    }
}
