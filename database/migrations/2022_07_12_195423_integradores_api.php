<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('integradores_apis', function (Blueprint $table) {
            $table->increments('ina_id');
            $table->uuid('uuid_ina_id');
            $table->string('ina_usuario', 100);
            $table->string('ina_api');
            $table->string('ina_senha');
            $table->string('ina_token');
            $table->unsignedInteger('fk_int_id_integrador');
            $table->foreign('fk_int_id_integrador')->references('int_id')->on('integradores');
            $table->timestamp('ina_criado_em')->useCurrent();
            $table->timestamp('ina_atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes($column = 'ina_deletado_em');
        });
    }

    public function down()
    {
        Schema::dropIfExists('integradores_apis');
    }
};
