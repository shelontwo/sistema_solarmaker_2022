<?php

use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('usuarios')->insert([
            'usu_nome' => 'D3T',
            'usu_email' => 'd3t@d3t.com.br',
            'usu_senha' => bcrypt('123456'),
            'usu_apelido' => 'admin',
            'fk_gru_id_grupo' => 1,
        ]);
    }
}