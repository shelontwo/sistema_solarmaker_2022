<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('modulos', function (Blueprint $table) {
            $table->increments('mod_id');
            $table->uuid('uuid_mod_id');
            $table->string('mod_nome', 100);
            $table->integer('mod_ordem')->default(0);
            $table->string('mod_icone')->nullable();
            $table->unsignedInteger('fk_mod_id_modulo')->nullable();
            $table->foreign('fk_mod_id_modulo')->references('mod_id')->on('modulos');
            $table->timestamp('mod_criado_em')->useCurrent();
            $table->timestamp('mod_atualizado_em')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down()
    {
        Schema::dropIfExists('modulos');
    }
};