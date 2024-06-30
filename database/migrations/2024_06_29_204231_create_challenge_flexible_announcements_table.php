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
        Schema::create('challenge_flexible_announcements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challenge_id');
            $table->unsignedBigInteger('challenge_flexible_id');
            $table->enum('custom_announcement_type', ['0', '1'])->default('0')->comment('0 -> email,1 -> notification');
            $table->integer('custom_announcement_number')->nullable()->comment('Number of days, week or month, from start to end. Though duration allows for post crossing timeline.');
            $table->enum('custom_announcement_duration', ['days', 'weeks', 'months'])->comment('Number of days, week or month, from start to end')->default('days')->nullable();
            $table->longText('custom_announcement_description')->nullable();
            $table->foreign('challenge_id')->references('id')->on('challenges')->onDelete('cascade');
            $table->foreign('challenge_flexible_id')->references('id')->on('challenge_timelines')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_flexible_announcements');
    }
};
