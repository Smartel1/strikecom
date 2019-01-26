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
            $table->bigInteger('date');
            $table->integer('views')->default(0);
            $table->string('source_link', 500)->nullable();
            $table->integer('conflict_id');
            $table->foreign('conflict_id')->references('id')->on('conflicts')->onDelete('cascade');
            $table->integer('event_status_id')->nullable();
            $table->foreign('event_status_id')->references('id')->on('event_statuses')->onDelete('set null');
            $table->integer('event_type_id')->nullable();
            $table->foreign('event_type_id')->references('id')->on('event_types')->onDelete('set null');
            $table->bigInteger('created_at')->nullable();
            $table->bigInteger('updated_at')->nullable();
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