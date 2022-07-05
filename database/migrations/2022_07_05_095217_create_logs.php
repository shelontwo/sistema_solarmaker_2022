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
            $table->bigIncrements('id');
            $table->unsignedInteger('fk_user_id')->nullable();
            $table->string('url', 200);
            $table->string('method', 50);
            $table->longText('request_json')->nullable();
            $table->longText('response_json')->nullable();
            $table->unsignedSmallInteger('status');
            $table->string('ip_address', 20);
            $table->timestamps();
            $table->foreign('fk_user_id')->references('id')->on('users');
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
