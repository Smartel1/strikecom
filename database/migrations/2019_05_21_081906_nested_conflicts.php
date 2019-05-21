<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NestedConflicts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('conflicts', function (Blueprint $table) {
            $table->integer('_lft')->nullable();
            $table->integer('_rgt')->nullable();
            $table->integer('parent_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('conflicts', function (Blueprint $table) {
            $table->dropColumn('_lft');
            $table->dropColumn('_rgt');
            $table->dropColumn('parent_id');
        });
    }
}
