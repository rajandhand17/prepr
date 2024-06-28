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
        Schema::create('channel_vendor_api_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_vendor_id')->references('id')->on('channel_vendors')->onDelete('cascade');
            $table->foreignId('channel_api_id')->references('id')->on('channel_apis')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_vendor_api_access');
    }
};
