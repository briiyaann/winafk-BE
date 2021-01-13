<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topups', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('amount');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('status')->default('pending');
            $table->text('reason')->nullable();
            $table->integer('approved_by')->nullable();
            $table->string('valid_id')->nullable();
            $table->string('receipt');
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
        Schema::dropIfExists('topups');
    }
}
