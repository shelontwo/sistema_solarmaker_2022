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
            'dis_nome' => 'D3T - Distribuidor',
        ]);
    }
}
