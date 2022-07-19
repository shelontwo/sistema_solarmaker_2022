<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('interfaces', function (Blueprint $table) {
            $table->increments('ite_id');
            $table->uuid('uuid_ite_id');
            $table->timestamp('ite_data');
            $table->string('ite_nsu', 100);
            $table->string('ite_usuario', 100);
            $table->string('ite_senha', 100);
            $table->unsignedInteger('fk_usi_id_usina')->nullable();
            $table->foreign('fk_usi_id_usina')->references('usi_id')->on('usinas');
            $table->unsignedInteger('fk_uco_id_unidade')->nullable();
            $table->foreign('fk_uco_id_unidade')->references('uco_id')->on('unidades_consumidoras');
            $table->unsignedInteger('fk_int_id_integrador');
            $table->foreign('fk_int_id_integrador')->references('int_id')->on('integradores');
            $table->timestamp('ite_criado_em')->useCurrent();
            $table->timestamp('ite_atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes($column = 'ite_deletado_em');
        });
    }

    public function down()
    {
        Schema::dropIfExists('interfaces');
    }
};
