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
            $table->string('ucf_consumida', 50);
            $table->string('ucf_faturada', 50);
            $table->decimal('ucf_tarifa');
            $table->string('ucf_energia', 50);
            $table->string('ucf_energia_injetada', 50);
            $table->string('ucf_situacao', 50);
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
