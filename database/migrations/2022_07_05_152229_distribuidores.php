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
            $table->string('dis_nome');
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
