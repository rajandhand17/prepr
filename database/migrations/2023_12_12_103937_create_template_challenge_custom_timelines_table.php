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
        Schema::create('template_challenge_custom_timelines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_challenge_id');
            $table->string('custom_timelines_title')->nullable();
            $table->string('custom_timelines_date', 255)->nullable();
            $table->longText('custom_timelines_description')->nullable();
            $table->enum('custom_timelines_duration', ['days', 'weeks', 'months'])->comment('Number of days, week or month, from start to end')->default('days');
            $table->enum('schedule_custom_notify', ['0', '1'])->comment('0 -> Day before submission deadline reminder, 1 -> Week before submission deadline reminder')->default('0');
            $table->foreign('template_challenge_id', 'fk_template_challenge_custom_timelines')->references('id')->on('template_challenges')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_challenge_custom_timelines');
    }
};
