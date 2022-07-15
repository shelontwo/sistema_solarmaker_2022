<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('unidades_consumidoras_fatura', function (Blueprint $table) {
            $table->increments('ucf_id');
            $table->uuid('uuid_ucf_id');
            $table->decimal('ucf_valor_faturado');
            $table->date('ucf_inicio_ciclo');
            $table->date('ucf_fim_ciclo');
            $table->decimal('ucf_valor_tarifa');
            $table->decimal('ucf_consumida');
            $table->decimal('ucf_faturada');
            $table->decimal('ucf_tarifa');
            $table->decimal('ucf_energia');
            $table->decimal('ucf_energia_injetada');
            $table->smallInteger('ucf_situacao');
            $table->string('ucf_nome_arquivo')->nullable();
            $table->string('ucf_arquivo')->nullable();
            $table->unsignedInteger('fk_uco_id_unidade');
            $table->foreign('fk_uco_id_unidade')->references('uco_id')->on('unidades_consumidoras');
            $table->timestamp('ucf_criado_em')->useCurrent();
            $table->timestamp('ucf_atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes($column = 'ucf_deletado_em');
        });
    }

    public function down()
    {
        Schema::dropIfExists('unidades_consumidoras_fatura');
    }
};
