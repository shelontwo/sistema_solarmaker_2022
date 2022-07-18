<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('webhooks', function (Blueprint $table) {
            $table->increments('web_id');
            $table->unsignedInteger('fk_cli_id_cliente')->nullable();
            $table->foreign('fk_cli_id_cliente')->references('cli_id')->on('clientes');
            $table->unsignedInteger('fk_usi_id_usina')->nullable();
            $table->foreign('fk_usi_id_usina')->references('usi_id')->on('usinas');
            $table->unsignedBigInteger('fk_log_id_log')->nullable();
            $table->foreign('fk_log_id_log')->references('log_id')->on('logs');
            $table->enum('web_status', ['pendente', 'enviando', 'enviado'])->default('pendente');
            $table->timestamp('web_criado_em')->useCurrent();
            $table->timestamp('web_atualizado_em')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down()
    {
        Schema::dropIfExists('webhooks');
    }
};
