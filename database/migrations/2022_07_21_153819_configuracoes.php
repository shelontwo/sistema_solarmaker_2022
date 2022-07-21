<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('configuracoes', function (Blueprint $table) {
            $table->increments('con_id');
            $table->uuid('uuid_con_id');
            $table->string('con_canal_id');
            $table->enum('con_tipo', ['geral', 'critico']);
            $table->boolean('con_ativo')->default(1);
            $table->unsignedInteger('fk_dis_id_distribuidor')->nullable();
            $table->foreign('fk_dis_id_distribuidor')->references('dis_id')->on('distribuidores');
            $table->unsignedInteger('fk_int_id_integrador')->nullable();
            $table->foreign('fk_int_id_integrador')->references('int_id')->on('integradores');
            $table->timestamp('con_criado_em')->useCurrent();
            $table->timestamp('con_atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes($column = 'con_deletado_em');
        });
    }

    public function down()
    {
        Schema::dropIfExists('configuracoes');
    }
};
