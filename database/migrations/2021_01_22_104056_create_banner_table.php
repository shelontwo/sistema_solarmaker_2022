<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannerTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('banner', function (Blueprint $table) {
      $table->increments('id');
      $table->boolean('active')->nullable()->default(0);
      $table->boolean('popup')->nullable()->default(0);
      $table->longText('description')->nullable();
      $table->string('image');
      $table->string('mobile_image');
      $table->string('link')->nullable();
      $table->string('button')->nullable();
      $table->string('title')->nullable();
      $table->integer('turn');
      $table->string('start_date')->nullable();
      $table->string('end_date')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
      Schema::dropIfExists('banner');
  }
}

