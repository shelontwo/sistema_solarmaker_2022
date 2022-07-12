<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('grupos', function (Blueprint $table) {
            $table->increments('gru_id');
            $table->uuid('uuid_gru_id');
            $table->string('gru_nome');
            $table->timestamp('gru_criado_em')->useCurrent();
            $table->timestamp('gru_atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes($column = 'gru_deletado_em');
        });
    }

    public function down()
    {
        Schema::dropIfExists('grupos');
    }
};