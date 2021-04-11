<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralPointLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('referral_point_logs');
        Schema::create('referral_point_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('bet_id')->nullable();
            $table->decimal('points', 20, 4)->default(0);
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            $table->foreign('bet_id')
                ->references('id')
                ->on('bets')
                ->onDelete('set null');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('referral_points');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referral_point_logs');

        Schema::table('users', function (Blueprint $table) {
            $table->decimal('referral_points', 20, 2)->default(0)->after('approved_referral_code');
        });
    }
}
