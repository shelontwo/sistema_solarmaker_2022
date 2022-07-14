<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chamados_comentario', function (Blueprint $table) {
            $table->increments('cco_id');
            $table->uuid('uuid_cco_id');
            $table->text('cco_comentario');
            $table->unsignedInteger('fk_usu_id_usuario');
            $table->foreign('fk_usu_id_usuario')->references('usu_id')->on('usuarios');
            $table->unsignedInteger('fk_cha_id_chamado');
            $table->foreign('fk_cha_id_chamado')->references('cha_id')->on('chamados');
            $table->timestamp('cco_criado_em')->useCurrent();
            $table->timestamp('cco_atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes($column = 'cco_deletado_em');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chamados_comentario');
    }
};