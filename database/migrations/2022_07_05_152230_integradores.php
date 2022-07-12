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
            $table->string('int_cnpj', 18);
            $table->string('int_cep', 10);
            $table->string('int_uf', 2);
            $table->string('int_cidade', 150);
            $table->string('int_bairro', 150);
            $table->string('int_rua');
            $table->smallInteger('int_numero');
            $table->string('int_complemento');
            $table->string('int_telefone', 20);
            $table->string('int_celular', 20);
            $table->string('int_email', 80);
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
