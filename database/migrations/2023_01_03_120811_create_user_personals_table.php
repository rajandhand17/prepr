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
        Schema::create('user_personals', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->text('about')->nullable();
            $table->string('gender')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->string('age')->nullable();
            $table->string('user_type')->nullable();
            $table->string('language')->nullable();
            $table->string('recent_immigrant')->nullable();
            $table->string('indigenous_group')->nullable();
            $table->string('visible_minority')->nullable();
            $table->string('disability')->nullable();
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
        Schema::dropIfExists('user_personals');
    }
};
