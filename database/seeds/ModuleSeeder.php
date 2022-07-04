<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $current_timestamp = date('Y-m-d h:i:s');

        DB::table('modules')->insert([
            [
                'id' => 1,
                'name' => 'Administração',
                'father_path' => '',
                'path' => 'admin',
                'father_order' => 99,
                'order' => 0,
                'icon' => 'fa fa-lock',
                'has_son' => 1,
                'created_at' => $current_timestamp,
                'updated_at' => $current_timestamp,
            ],
            [
                'id' => 2,
                'name' => 'Grupos de Usuários',
                'father_path' => 'admin',
                'path' => 'groups',
                'father_order' => 99,
                'order' => 1,
                'icon' => '',
                'has_son' => 0,
                'created_at' => $current_timestamp,
                'updated_at' => $current_timestamp,
            ],
            [
                'id' => 3,
                'name' => 'Usuários',
                'father_path' => 'admin',
                'path' => 'users',
                'father_order' => 99,
                'order' => 2,
                'icon' => '',
                'has_son' => 0,
                'created_at' => $current_timestamp,
                'updated_at' => $current_timestamp,
            ],          
        ]);

        DB::table('groups')->insert([
            'name' => 'Administrador',
            'created_at' => $current_timestamp,
            'updated_at' => $current_timestamp,
        ]);

        $modules = DB::table('modules')->get();
        $groups = DB::table('groups')->get();
        foreach ($modules as $module) {
            foreach ($groups as $group) {
                DB::table('group_module')->insert([
                    'group_id' => $group->id, 'module_id' => $module->id,
                ]);
            }
        }
    }
}
