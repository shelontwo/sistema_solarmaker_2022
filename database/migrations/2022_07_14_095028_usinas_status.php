<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usinas_status', function (Blueprint $table) {
            $table->increments('uss_id');
            $table->uuid('uuid_uss_id');
            $table->string('uss_nome', 50);
            $table->smallInteger('uss_tipo'); // 0 - não informado / 1 - normal / 2 - alerta / 3 - falha 
            $table->timestamp('uss_criado_em')->useCurrent();
            $table->timestamp('uss_atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes($column = 'uss_deletado_em');
        });
    }

    public function down()
    {
        Schema::dropIfExists('usinas_status');
    }
};
