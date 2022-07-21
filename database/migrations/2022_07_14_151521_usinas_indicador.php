<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usinas_indicador', function (Blueprint $table) {
            $table->increments('uin_id');
            $table->uuid('uuid_uin_id');
            $table->date('uin_data');
            $table->time('uin_hora');
            $table->string('uin_campo', 150);
            $table->string('uin_valor', 150);
            $table->unsignedInteger('fk_usi_id_usina');
            $table->foreign('fk_usi_id_usina')->references('usi_id')->on('usinas');
            $table->timestamp('uin_criado_em')->useCurrent();
            $table->timestamp('uin_atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes($column = 'uin_deletado_em');
        });
    }

    public function down()
    {
        Schema::dropIfExists('usinas_indicador');
    }
};