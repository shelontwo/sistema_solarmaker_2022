<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->bigIncrements('log_id');
            $table->uuid('uuid_log_id');
            $table->unsignedInteger('fk_usu_id_usuario')->nullable();
            $table->string('log_url', 200);
            $table->string('log_method', 50);
            $table->longText('log_request_json')->nullable();
            $table->longText('log_response_json')->nullable();
            $table->unsignedSmallInteger('log_status');
            $table->string('log_ip_address', 20);
            $table->timestamp('log_criado_em')->useCurrent();
            $table->timestamp('log_atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->foreign('fk_usu_id_usuario')->references('usu_id')->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs');
    }
};
