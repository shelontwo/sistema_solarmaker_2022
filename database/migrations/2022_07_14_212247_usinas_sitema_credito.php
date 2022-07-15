<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usinas_sitema_credito', function (Blueprint $table) {
            $table->increments('usc_id');
            $table->uuid('uuid_usc_id');
            $table->date('usc_vigencia');
            $table->decimal('usc_percentual');
            $table->unsignedInteger('fk_usi_id_usina');
            $table->foreign('fk_usi_id_usina')->references('usi_id')->on('usinas');
            $table->unsignedInteger('fk_uco_id_unidade');
            $table->foreign('fk_uco_id_unidade')->references('uco_id')->on('unidades_consumidoras');
            $table->timestamp('usc_criado_em')->useCurrent();
            $table->timestamp('usc_atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes($column = 'usc_deletado_em');
        });
    }

    public function down()
    {
        Schema::dropIfExists('usinas_sitema_credito');
    }
};