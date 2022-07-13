<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->increments('cli_id');
            $table->uuid('uuid_cli_id')->unique();
            $table->string('cli_nome', 150);
            $table->string('cli_cep', 10)->nullable();
            $table->string('cli_uf', 2)->nullable();
            $table->string('cli_cidade', 150)->nullable();
            $table->string('cli_bairro', 150)->nullable();
            $table->string('cli_rua')->nullable();
            $table->string('cli_numero', 50)->nullable();
            $table->string('cli_complemento')->nullable();
            $table->string('cli_telefone', 20)->nullable();
            $table->string('cli_celular', 20)->nullable();
            $table->string('cli_email', 80)->nullable();
            $table->string('cli_usuario', 80);
            $table->string('cli_senha', 80)->nullable();
            $table->boolean('cli_alterar_senha')->default(false);
            $table->unsignedInteger('fk_int_id_integrador');
            $table->foreign('fk_int_id_integrador')->references('int_id')->on('integradores');
            $table->timestamp('cli_criado_em')->useCurrent();
            $table->timestamp('cli_atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes($column = 'cli_deletado_em');
        });
    }

    public function down()
    {
        Schema::dropIfExists('clientes');
    }
};
