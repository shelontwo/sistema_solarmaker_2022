<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chamados', function (Blueprint $table) {
            $table->increments('cha_id');
            $table->uuid('uuid_cha_id');
            $table->boolean('cha_status')->default(false);
            $table->text('cha_descricao')->nullable();
            $table->text('cha_solucao')->nullable();
            $table->unsignedInteger('fk_cli_id_cliente');
            $table->foreign('fk_cli_id_cliente')->references('cli_id')->on('clientes');
            $table->timestamp('cha_finalizado_em')->nullable();
            $table->timestamp('cha_criado_em')->useCurrent();
            $table->timestamp('cha_atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes($column = 'cha_deletado_em');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chamados');
    }
};