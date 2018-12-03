<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventCommentPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_comment_photos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url', 500);
            $table->integer('event_comment_id');
            $table->foreign('event_comment_id')->references('id')->on('event_comments')->onDelete('cascade');
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
        Schema::dropIfExists('event_comment_photos');
    }
}
