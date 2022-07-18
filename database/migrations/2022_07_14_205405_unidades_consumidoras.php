<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('unidades_consumidoras', function (Blueprint $table) {
            $table->increments('uco_id');
            $table->uuid('uuid_uco_id');
            $table->string('uco_codigo', 150);
            $table->string('uco_nome', 150);
            $table->string('uco_classificacao', 50);
            $table->smallInteger('uco_tipo');
            $table->string('uco_modalidade', 50);
            $table->unsignedInteger('fk_con_id_concessionaria');
            $table->foreign('fk_con_id_concessionaria')->references('con_id')->on('concessionarias');
            $table->timestamp('uco_criado_em')->useCurrent();
            $table->timestamp('uco_atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes($column = 'uco_deletado_em');
        });
    }

    public function down()
    {
        Schema::dropIfExists('unidades_consumidoras');
    }
};