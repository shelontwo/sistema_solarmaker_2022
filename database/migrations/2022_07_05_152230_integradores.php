<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('integradores', function (Blueprint $table) {
            $table->increments('int_id');
            $table->uuid('uuid_int_id')->unique();
            $table->string('int_nome');
            $table->unsignedInteger('fk_dis_id_distribuidor');
            $table->foreign('fk_dis_id_distribuidor')->references('dis_id')->on('distribuidores');
            $table->timestamp('int_criado_em')->useCurrent();
            $table->timestamp('int_atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes($column = 'int_deletado_em');
        });
    }

    public function down()
    {
        Schema::dropIfExists('integradores');
    }
};
