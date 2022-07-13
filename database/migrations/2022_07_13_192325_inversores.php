<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inversores', function (Blueprint $table) {
            $table->increments('inv_id');
            $table->uuid('uuid_inv_id');
            $table->string('inv_marca', 150)->nullable();
            $table->string('inv_modelo', 150)->nullable();
            $table->boolean('inv_status')->default(true);
            $table->string('inv_potencia', 150)->nullable();
            $table->string('inv_serial', 150)->nullable();
            $table->timestamp('inv_garantia')->nullable();
            $table->unsignedInteger('fk_int_id_integrador');
            $table->foreign('fk_int_id_integrador')->references('int_id')->on('integradores');
            $table->timestamp('inv_criado_em')->useCurrent();
            $table->timestamp('inv_atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes($column = 'inv_deletado_em');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inversores');
    }
};