<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('integradores', function (Blueprint $table) {
            $table->increments('int_id');
            $table->uuid('uuid_int_id')->unique();
            $table->string('int_nome', 150);
            $table->string('int_nome_fantasia', 150);
            $table->string('int_cnpj', 18)->nullable();
            $table->string('int_cep', 10)->nullable();
            $table->string('int_uf', 2)->nullable();
            $table->string('int_cidade', 150)->nullable();
            $table->string('int_bairro', 150)->nullable();
            $table->string('int_rua')->nullable();
            $table->string('int_numero', 50)->nullable();
            $table->string('int_complemento')->nullable();
            $table->string('int_telefone', 20)->nullable();
            $table->string('int_celular', 20)->nullable();
            $table->string('int_email', 80)->nullable();
            $table->string('int_imagem')->nullable();
            $table->unsignedInteger('fk_dis_id_distribuidor');
            $table->foreign('fk_dis_id_distribuidor')->references('dis_id')->on('distribuidores');
            $table->timestamp('int_criado_em')->useCurrent();
            $table->timestamp('int_atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes($column = 'int_deletado_em');
        });
    }

    public function down()
    {
        Schema::dropIfExists('integradores');
    }
};
