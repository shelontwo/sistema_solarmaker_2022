<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usinas_inversor', function (Blueprint $table) {
            $table->increments('inu_id');
            $table->uuid('uuid_inu_id');
            $table->integer('inu_painel_quantidade');
            $table->string('inu_painel_potencia', 150);
            $table->unsignedInteger('fk_usi_id_usina');
            $table->foreign('fk_usi_id_usina')->references('usi_id')->on('usinas');
            $table->unsignedInteger('fk_inv_id_inversor');
            $table->foreign('fk_inv_id_inversor')->references('inv_id')->on('inversores');
            $table->timestamp('inu_criado_em')->useCurrent();
            $table->timestamp('inu_atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes($column = 'inu_deletado_em');
        });
    }

    public function down()
    {
        Schema::dropIfExists('usinas_inversor');
    }
};