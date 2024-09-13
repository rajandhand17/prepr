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
        Schema::create('go1_webhook_metadata', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->dateTime('fired_at')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('go1_user_resource_progress_id')->references('id')->on('go1_user_resource_progress')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('go1_webhook_metadata');
    }
};
