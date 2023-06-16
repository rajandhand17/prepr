<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lab_challenges', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('lab_id')->nullable();
            $table->bigInteger('challenge_id')->nullable();
            $table->bigInteger('challenge_path_id')->nullable();
            $table->enum('status', ['0', '1']);
            $table->string('sequence_no')->nullable();
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
        Schema::dropIfExists('lab_challenges');
    }
};
