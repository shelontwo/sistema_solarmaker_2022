<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('concessionarias', function (Blueprint $table) {
            $table->increments('con_id');
            $table->uuid('uuid_con_id');
            $table->string('con_nome');
            $table->string('con_cnpj', 18)->nullable();
            $table->string('con_uf', 2)->nullable();
            $table->timestamp('con_criado_em')->useCurrent();
            $table->timestamp('con_atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes($column = 'con_deletado_em');
        });
    }

    public function down()
    {
        Schema::dropIfExists('concessionarias');
    }
};