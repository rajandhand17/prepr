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
            $table->bigInteger('challenge_id');
            $table->string('custom_timelines_title');
            $table->timestamp('custom_timelines_date');
            $table->longText('custom_timelines_description');
            $table->enum('custom_timelines_duration', ['days', 'weeks', 'months'])->comment('Number of days, week or month, from start to end')->default('0');
            $table->enum('schedule_custom_notify', ['0', '1'])->comment('0 -> Day before submission deadline reminder, 1 -> Week before submission deadline reminder')->default('0');
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
