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
        Schema::create('lab_achievements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("lab_id");
            $table->text("achievement_name");
            $table->integer("achievement_points");
            $table->text("achievement_condition")->nullable();
            $table->string("achievement_image")->nullable();
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
        Schema::dropIfExists('lab_achievements');
    }
};
