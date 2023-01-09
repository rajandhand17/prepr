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
        Schema::create('resource_collection_for_lab_and_challenges', function (Blueprint $table) {
            $table->id();
            $table->integer("user_id");
            $table->integer("lab_id");
            $table->integer("challenge_id");
            $table->integer("resource_collection_id");
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
        Schema::dropIfExists('resource_collection_for_lab_and_challenges');
    }
};
