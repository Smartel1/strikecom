<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_versions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('version');
            $table->integer('client_id');
            $table->boolean('required');
            $table->string('description_ru', 500);
            $table->string('description_en', 500);
            $table->string('description_es', 500);
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
        Schema::dropIfExists('client_versions');
    }
}
