<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseTestSeeder extends Seeder
{
    public function run()
    {
        $this->call(Test\DistribuidoresSeeder::class);
        $this->call(Test\IntegradoresSeeder::class);
    }
}
