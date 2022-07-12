<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('distribuidores', function (Blueprint $table) {
            $table->increments('dis_id');
            $table->uuid('uuid_dis_id')->unique();
            $table->string('dis_nome', 150);
            $table->string('dis_nome_fantasia', 150);
            $table->string('dis_cnpj', 18);
            $table->string('dis_cep', 10);
            $table->string('dis_uf', 2);
            $table->string('dis_cidade', 150);
            $table->string('dis_bairro', 150);
            $table->string('dis_rua');
            $table->smallInteger('dis_numero');
            $table->string('dis_complemento');
            $table->string('dis_telefone', 20);
            $table->string('dis_celular', 20);
            $table->string('dis_email', 80);
            $table->string('dis_imagem')->nullable();
            $table->timestamp('dis_criado_em')->useCurrent();
            $table->timestamp('dis_atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes($column = 'dis_deletado_em');
        });
    }

    public function down()
    {
        Schema::dropIfExists('distribuidores');
    }
};
