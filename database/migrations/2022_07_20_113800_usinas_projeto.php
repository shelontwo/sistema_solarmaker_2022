<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usinas_projeto', function (Blueprint $table) {
            $table->increments('usp_id');
            $table->uuid('uuid_usp_id');
            $table->string('usp_potencia', 150);
            $table->string('usp_eficiencia', 150);
            $table->date('usp_data_instalacao');
            $table->decimal('usp_total_investido');
            $table->decimal('usp_tarifa_referencia');
            $table->string('usp_rsi_jan', 10);
            $table->string('usp_rsi_fev', 10);
            $table->string('usp_rsi_mar', 10);
            $table->string('usp_rsi_abr', 10);
            $table->string('usp_rsi_mai', 10);
            $table->string('usp_rsi_jun', 10);
            $table->string('usp_rsi_jul', 10);
            $table->string('usp_rsi_ago', 10);
            $table->string('usp_rsi_set', 10);
            $table->string('usp_rsi_out', 10);
            $table->string('usp_rsi_nov', 10);
            $table->string('usp_rsi_dez', 10);
            $table->unsignedInteger('fk_usi_id_usina');
            $table->foreign('fk_usi_id_usina')->references('usi_id')->on('usinas');
            $table->timestamp('usp_criado_em')->useCurrent();
            $table->timestamp('usp_atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes($column = 'usp_deletado_em');
        });
    }

    public function down()
    {
        Schema::dropIfExists('usinas_projeto');
    }
};