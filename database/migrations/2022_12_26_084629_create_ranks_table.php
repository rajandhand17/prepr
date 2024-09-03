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
        Schema::create('ranks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('fr_CA_title')->nullable();
            $table->string('description');
            $table->string('fr_CA_description')->nullable();
            $table->text('image')->nullable();
            $table->string('category')->nullable();
            $table->integer('point');
            $table->integer('no_of_use')->nullable();
            $table->enum('status', ['0', '1'])->default('1')->comment('0 ->in-active, 1 -> active');
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
        Schema::dropIfExists('ranks');
    }
};
