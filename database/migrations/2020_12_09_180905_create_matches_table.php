<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('game_type_id');
            $table->integer('league_id');
            $table->datetime('schedule');
            $table->integer('fee');
            $table->integer('match_count');
            $table->string('label');
            $table->string('status')->default('upcoming');
            $table->string('status_label')->nullable();
            $table->string('current_round')->nullable();
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
        Schema::dropIfExists('matches');
    }
}
