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
        Schema::create('channel_apis', function (Blueprint $table) {
            $table->id();
            $table->string('api_name')->unique();
            $table->string('api_slug')->unique();
            $table->boolean('is_active')->default(true)->comment('0-> no , 1-> yes');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_apis');
    }
};
