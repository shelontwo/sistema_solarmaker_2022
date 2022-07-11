<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class ModuleSeeder extends Seeder
{
    public function run()
    {
        $current_timestamp = date('Y-m-d h:i:s');

        DB::table('modulos')->insert([
            [
                'mod_id' => 1,
                'uuid_mod_id' => Str::uuid()->toString(),
                'mod_nome' => 'Administração',
                'mod_ordem' => 0,
                'fk_mod_id_modulo' => null
            ],
            [
                'mod_id' => 2,
                'uuid_mod_id' => Str::uuid()->toString(),
                'mod_nome' => 'Grupos de Usuários',
                'mod_ordem' => 1,
                'fk_mod_id_modulo' => 1
            ],
            [
                'mod_id' => 3,
                'uuid_mod_id' => Str::uuid()->toString(),
                'mod_nome' => 'Usuários',
                'mod_ordem' => 2,
                'fk_mod_id_modulo' => 1
            ],          
        ]);

        DB::table('grupos')->insert([
            'gru_nome' => 'Administrador',
            'uuid_gru_id' => Str::uuid()->toString(),
        ]);

        $modules = DB::table('modulos')->get();
        $groups = DB::table('grupos')->get();
        foreach ($modules as $module) {
            foreach ($groups as $group) {
                DB::table('grupos_modulos')->insert([
                    'uuid_gmo_id' => Str::uuid()->toString(),
                    'fk_gru_id_grupo' => $group->gru_id, 'fk_mod_id_modulo' => $module->mod_id,
                ]);
            }
        }
    }
}