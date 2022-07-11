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
            'int_nome' => 'D3T - Integrador',
            'fk_dis_id_distribuidor' => 1,
        ]);
    }
}
