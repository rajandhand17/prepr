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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->text('about')->nullable();
            $table->string('gender', 255)->nullable();
            $table->string('date_of_birth')->nullable();
            $table->string('age')->nullable();
            $table->string('status', 255)->nullable();
            $table->string('user_type')->nullable();
            $table->string('language', 255)->nullable();
            $table->boolean('recent_immigrant')->default(0)->nullable();
            $table->boolean('indigenous_group')->default(0)->nullable();
            $table->boolean('visible_minority')->default(0)->nullable();
            $table->boolean('disability')->default(0)->nullable();
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
