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
        Schema::create('airmeet_event_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('airmeet_event_id')->constrained('airmeet_events')->onDelete('cascade');
            $table->string('airmeet_event_uuid');
            $table->foreignId('attendee_id')->constrained('users')->onDelete('cascade');
            $table->string('event_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airmeet_event_attendees');
    }
};
