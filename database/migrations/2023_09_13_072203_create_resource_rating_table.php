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
        Schema::create('resource_rating', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('resource_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('rating_id')->nullable();
            $table->timestamps();
            $table->softDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_rating');
    }
};
