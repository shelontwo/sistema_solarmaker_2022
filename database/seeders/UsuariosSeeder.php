<?php
namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsuariosSeeder extends Seeder
{
    public function run()
    {
        DB::table('usuarios')->insert([
            'uuid_usu_id' => Str::uuid()->toString(),
            'usu_nome' => 'D3T',
            'usu_apelido' => 'admin',
            'usu_email' => 'd3t@d3t.com.br',
            'password' => bcrypt('123456'),
            'fk_gru_id_grupo' => 1,
        ]);
    }
}