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
        Schema::create('lab_achievement_wins', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("lab_id");
            $table->bigInteger("achievement_id");
            $table->bigInteger("user_id");
            $table->text("lab_condition")->comment("user achieved on which condition");
            $table->integer("lab_points")->nullable()->comment("user achieved points");
            $table->string("lab_achievement_image")->nullable()->comment("lab achievements image");
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
        Schema::dropIfExists('lab_achievement_wins');
    }
};
