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
        Schema::create('organisations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->nullable();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->text('vanity_slug')->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('profile_image')->nullable();
            $table->text('about')->nullable();
            $table->integer('category')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->text('address')->nullable();
            $table->text('vanity_link')->nullable();
            $table->enum('status', ['0', '1'])->nullable()->default('0');
            $table->integer('labs_limit')->nullable();
            $table->integer('challenges_limit')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organisations');
    }
};
