<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubmatchOddsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submatch_odds', function (Blueprint $table) {
            $table->id();
            $table->integer('sub_match_id');
            $table->integer('game_id');
            $table->integer('team_id');
            $table->decimal('bets', 11, 2)->default(0);
            $table->decimal('percentage', 5, 2);
            $table->decimal('odds', 5, 2);
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
        Schema::dropIfExists('submatch_odds');
    }
}
