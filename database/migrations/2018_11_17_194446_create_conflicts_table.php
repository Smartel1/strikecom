<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConflictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conflicts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->double('latitude');
            $table->double('longitude');
            $table->string('company_name', 500);
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->integer('conflict_reason_id')->nullable();
            $table->foreign('conflict_reason_id')->references('id')->on('conflict_reasons')->onDelete('set null');
            $table->integer('conflict_result_id')->nullable();
            $table->foreign('conflict_result_id')->references('id')->on('conflict_results')->onDelete('set null');
            $table->integer('industry_id')->nullable();
            $table->foreign('industry_id')->references('id')->on('industries')->onDelete('set null');
            $table->integer('region_id')->nullable();
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('set null');
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
        Schema::dropIfExists('conflicts');
    }
}
