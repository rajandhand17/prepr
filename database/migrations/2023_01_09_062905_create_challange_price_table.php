<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('challange_price', function (Blueprint $table) {
            $table->id();
            $table->integer('challenge_id')->default(0);
            $table->enum('type', ['incentive', 'participation'])->nullable();
            $table->string('name')->default(null);
            $table->string('prize')->default(null);
            $table->string('points')->default(null);
            $table->string('trophy')->default("front/img/ic_trophies.png");
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
        Schema::dropIfExists('challange_price');
    }
};
