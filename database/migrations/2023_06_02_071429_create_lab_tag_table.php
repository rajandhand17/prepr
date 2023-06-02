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
        Schema::create('lab_tag', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('lab_id');
            $table->integer('tag');
            $table->index(['user_id']);
            $table->index(['lab_id']);
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
        Schema::dropIfExists('lab_tag');
    }
};
