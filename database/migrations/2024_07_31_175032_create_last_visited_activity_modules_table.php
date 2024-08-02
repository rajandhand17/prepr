<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('last_visited_activity_modules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->bigInteger('module_id');
            $table->enum('module_type', ['0', '1', '2', '3', '4', '5', '6', '7'])->comment('0 -> Labs, 1 -> Lab Programs, 2 -> Challenges, 3 -> Challenge Paths, 4 -> Resource Modules, 5 -> Resource Collections, 6 -> Resource Groups, 7 -> Projects');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('last_visited_activity_modules');
    }
};
