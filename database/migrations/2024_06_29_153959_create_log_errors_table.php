<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('log_errors', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->string('level');
            $table->string('context')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // User ID if logged in
            $table->string('route')->nullable(); // Route where error occurred
            $table->string('ip')->nullable(); // IP address of request
            $table->timestamp('time')->nullable(); // Timestamp
            $table->string('file')->nullable(); // File name where error occurred
            $table->integer('line')->nullable(); // Line number where error occurred
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_errors');
    }
};
