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
        Schema::create('challenge_custom_timelines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challenge_id');
            $table->string('custom_timelines_title')->nullable();
            $table->integer('custom_timelines_number')->nullable();
            $table->longText('custom_timelines_description')->nullable();
            $table->enum('custom_timelines_duration', ['days', 'weeks', 'months'])->comment('Number of days, week or month, from start to end')->default('days');
            $table->enum('schedule_custom_notify', ['0', '1'])->comment('0 -> no, 1 -> yes')->default('0');
            $table->foreign('challenge_id')->references('id')->on('challenges')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_custom_timelines');
    }
};
