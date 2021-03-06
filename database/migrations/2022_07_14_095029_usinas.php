<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usinas', function (Blueprint $table) {
            $table->increments('usi_id');
            $table->uuid('uuid_usi_id')->unique();
            $table->string('usi_nome', 150);
            $table->string('usi_cep', 10)->nullable();
            $table->string('usi_uf', 2)->nullable();
            $table->string('usi_cidade', 150)->nullable();
            $table->string('usi_bairro', 150)->nullable();
            $table->string('usi_rua')->nullable();
            $table->string('usi_complemento')->nullable();
            $table->string('usi_numero', 50)->nullable();
            $table->string('usi_latitude', 25)->nullable();
            $table->string('usi_longitude', 25)->nullable();
            $table->boolean('usi_desativada')->default(false);
            $table->string('usi_webhook_url')->nullable();
            $table->unsignedInteger('fk_int_id_integrador');
            $table->foreign('fk_int_id_integrador')->references('int_id')->on('integradores');
            $table->unsignedInteger('fk_cli_id_cliente');
            $table->foreign('fk_cli_id_cliente')->references('cli_id')->on('clientes');
            $table->unsignedInteger('fk_uss_id_status');
            $table->foreign('fk_uss_id_status')->references('uss_id')->on('usinas_status');
            $table->timestamp('usi_desativado_em')->nullable();
            $table->timestamp('usi_criado_em')->useCurrent();
            $table->timestamp('usi_atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes($column = 'usi_deletado_em');
        });
    }

    public function down()
    {
        Schema::dropIfExists('usinas');
    }
};
