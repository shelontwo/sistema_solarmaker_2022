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
                'id' => 11,
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
                'id' => 12,
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
                'id' => 13,
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
            [
                'id' => 14,
                'name' => 'Banner',
                'father_path' => '',
                'path' => 'banner',
                'father_order' => 0,
                'order' => 1,
                'icon' => 'fa fa-picture-o',
                'has_son' => 0,
                'created_at' => $current_timestamp,
                'updated_at' => $current_timestamp,
            ],
            [
                'id' => 15,
                'name' => 'Blog',
                'father_path' => '',
                'path' => 'blog',
                'father_order' => 1,
                'order' => 1,
                'icon' => 'fa fa-newspaper-o',
                'has_son' => 1,
                'created_at' => $current_timestamp,
                'updated_at' => $current_timestamp,
            ],
            [
                'id' => 16,
                'name' => 'Categorias',
                'father_path' => 'blog',
                'path' => 'blog_categories',
                'father_order' => 1,
                'order' => 1,
                'icon' => '',
                'has_son' => 0,
                'created_at' => $current_timestamp,
                'updated_at' => $current_timestamp,
            ],
            [
                'id' => 17,
                'name' => 'Postagens',
                'father_path' => 'blog',
                'path' => 'blog_posts',
                'father_order' => 1,
                'order' => 2,
                'icon' => '',
                'has_son' => 0,
                'created_at' => $current_timestamp,
                'updated_at' => $current_timestamp,
            ],            
            [
                'id' => 22,
                'name' => 'Configurações',
                'father_path' => '',
                'path' => 'configurations',
                'father_order' => 98,
                'order' => 1,
                'icon' => 'fa fa-cog',
                'has_son' => 0,
                'created_at' => $current_timestamp,
                'updated_at' => $current_timestamp,
            ],            
            [
                'id' => 29,
                'name' => 'Páginas',
                'father_path' => '',
                'path' => 'pages',
                'father_order' => 97,
                'order' => 1,
                'icon' => 'fa fa-file-text-o',
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
