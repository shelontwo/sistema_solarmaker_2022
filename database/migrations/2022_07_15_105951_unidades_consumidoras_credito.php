<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('unidades_consumidoras_credito', function (Blueprint $table) {
            $table->increments('ucc_id');
            $table->uuid('uuid_ucc_id');
            $table->decimal('ucc_quantidade');
            $table->date('ucc_vigencia');
            $table->smallInteger('ucc_posto_tarifario');
            $table->text('ucc_observacao');
            $table->unsignedInteger('fk_usi_id_usina');
            $table->foreign('fk_usi_id_usina')->references('usi_id')->on('usinas');
            $table->unsignedInteger('fk_uco_id_unidade');
            $table->foreign('fk_uco_id_unidade')->references('uco_id')->on('unidades_consumidoras');
            $table->timestamp('ucc_criado_em')->useCurrent();
            $table->timestamp('ucc_atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes($column = 'ucc_deletado_em');
        });
    }

    public function down()
    {
        Schema::dropIfExists('unidades_consumidoras_credito');
    }
};
