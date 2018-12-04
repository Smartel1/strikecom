<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('content');
            $table->date('date');
            $table->integer('views')->default(0);
            $table->string('source_link', 500)->nullable();
            $table->integer('conflict_id')->nullable();
            $table->foreign('conflict_id')->references('id')->on('conflicts')->onDelete('cascade');
            $table->integer('event_status_id')->nullable();
            $table->foreign('event_status_id')->references('id')->on('event_statuses')->onDelete('set null');
            $table->integer('event_type_id')->nullable();
            $table->foreign('event_type_id')->references('id')->on('event_types')->onDelete('set null');
            $table->integer('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
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
        Schema::dropIfExists('events');
    }
}
