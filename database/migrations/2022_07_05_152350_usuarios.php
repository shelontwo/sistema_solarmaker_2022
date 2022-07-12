<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->increments('usu_id');
            $table->uuid('uuid_usu_id')->unique();
            $table->string('usu_nome');
            $table->string('usu_apelido')->unique();
            $table->string('usu_email')->unique();
            $table->string('password');
            $table->unsignedInteger('fk_gru_id_grupo');
            $table->unsignedInteger('fk_dis_id_distribuidor')->nullable();
            $table->unsignedInteger('fk_int_id_integrador')->nullable();
            $table->string('usu_imagem')->nullable();
            $table->text('usu_token')->nullable();
            $table->foreign('fk_gru_id_grupo')->references('gru_id')->on('grupos');
            $table->foreign('fk_dis_id_distribuidor')->references('dis_id')->on('distribuidores');
            $table->foreign('fk_int_id_integrador')->references('int_id')->on('integradores');
            $table->timestamp('usu_data_referencia')->nullable();
            $table->smallInteger('usu_dias_expiracao')->nullable();
            $table->timestamp('usu_criado_em')->useCurrent();
            $table->timestamp('usu_atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes($column = 'usu_deletado_em');
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
};