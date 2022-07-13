<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('grupos_modulos', function (Blueprint $table) {
            $table->increments('gmo_id');
            $table->unsignedInteger('fk_mod_id_modulo');
            $table->foreign('fk_mod_id_modulo')->references('mod_id')->on('modulos');
            $table->unsignedInteger('fk_gru_id_grupo');
            $table->foreign('fk_gru_id_grupo')->references('gru_id')->on('grupos');
            $table->timestamp('gmo_criado_em')->useCurrent();
            $table->timestamp('gmo_atualizado_em')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down()
    {
        Schema::dropIfExists('grupos_modulos');
    }
};