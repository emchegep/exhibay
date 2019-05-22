<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('topic');
            $table->string('description')->nullable();
            $table->string('project_name')->nullable();
            $table->string('poster_pdf_url');
            $table->string('poster_image_url');
            $table->string('pdf_image_url')->nullable();
            $table->string('poster_video_url')->nullable();
            $table->bigInteger('user_id')->unsigned();


            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posters');
    }
}
