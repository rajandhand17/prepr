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
        Schema::create('challenge_timelines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challenge_id');
            $table->enum('timeline_type', ['0', '1'])->comment('0 -> flexible, 1 -> restricted');
            $table->string('start_date', 255)->nullable()->comment('Date of restricted challenge, start challenge');
            $table->longText('start_date_description')->nullable();
            $table->string('registration_deadline_date', 255)->nullable()->comment('Date of restricted challenge,ending to start challenge');
            $table->longText('registration_deadline_date_description')->nullable();
            $table->string('submission_deadline_date', 255)->nullable()->comment('Date of restricted challenge,submit challenge');
            $table->longText('submission_deadline_date_description')->nullable();
            $table->integer('challenge_duration')->nullable()->comment('Duration of challenge, can be null only if challenge is flex type and has no end date');
            $table->integer('flexible_date_number')->nullable()->comment('Number of days, week or month, from start to end. Though duration allows for post crossing timeline.');
            $table->enum('flexible_date_duration', ['days', 'weeks', 'months'])->comment('Number of days, week or month, from start to end')->default('days')->nullable();
            $table->enum('automatic_alert', ['0', '1'])->comment('0 -> Day before submission deadline reminder, 1 -> Week before submission deadline reminder')->default('0');
            $table->string('flexible_expire_deadline', 255)->nullable()->comment('Date of flexible challenge expiration if any');
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
        Schema::dropIfExists('challenge_timelines');
    }
};
