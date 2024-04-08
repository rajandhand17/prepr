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
        Schema::create('user_resource_progress_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('resource_module_id')->references('id')->on('resource_modules')->onDelete('cascade');
            $table->string('completion_status')->nullable();
            $table->string('lesson_status')->nullable();
            $table->float('score_raw')->nullable();
            $table->string('session_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_resource_progress_tracking');
    }
};
