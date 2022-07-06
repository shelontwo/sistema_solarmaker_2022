<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->increments('usu_id');
            $table->uuid('uuid_usu_id');
            $table->string('usu_nome');
            $table->string('usu_apelido')->unique();
            $table->string('usu_email')->unique();
            $table->string('usu_senha');
            $table->unsignedInteger('fk_gru_id_grupo');
            $table->string('usu_imagem')->nullable();
            $table->string('usu_token')->nullable();
            $table->foreign('fk_gru_id_grupo')->references('gru_id')->on('grupos');
            $table->timestamp('usu_criado_em')->useCurrent();
            $table->timestamp('usu_atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes($column = 'usu_deletado_em');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};