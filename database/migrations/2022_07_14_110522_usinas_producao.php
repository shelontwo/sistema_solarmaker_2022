<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usinas_producao', function (Blueprint $table) {
            $table->increments('upr_id');
            $table->uuid('uuid_upr_id');
            $table->timestamp('upr_data');
            $table->decimal('upr_producao');
            $table->tinyInteger('upr_tipo')->default(1); // 1 - produção diária | 2 - produção instantanea
            $table->unsignedInteger('fk_usi_id_usina');
            $table->foreign('fk_usi_id_usina')->references('usi_id')->on('usinas');
            $table->timestamp('upr_criado_em')->useCurrent();
            $table->timestamp('upr_atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes($column = 'upr_deletado_em');
        });
    }

    public function down()
    {
        Schema::dropIfExists('usinas_producao');
    }
};