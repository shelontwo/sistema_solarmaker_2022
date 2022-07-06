<?php
namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('usuarios')->insert([
            'uuid_usu_id' => Str::uuid()->toString(),
            'usu_nome' => 'D3T',
            'usu_email' => 'd3t@d3t.com.br',
            'usu_senha' => bcrypt('123456'),
            'usu_apelido' => 'admin',
            'fk_gru_id_grupo' => 1,
        ]);
    }
}