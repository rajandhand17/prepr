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
        Schema::create('challenge_announcements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challenge_id');
            $table->string('subject', 255)->nullable();
            $table->json('to_recipient_ids')->comment('Recipient ids, to whom announcement needs to be sent');
            $table->enum('sent_by', ['0', '1', '2'])->default('2')->comment('0 -> email, 1 -> inbox, 2 -> both');
            $table->text('description')->comment('Announcement description')->nullable();
            $table->string('schedule_at', 255)->nullable()->comment('When do the announcement scheduled at?');
            $table->enum('status', ['0', '1', '2'])->default('0')->comment('0 -> Send, 1 -> Draft, 2 -> Scheduled');
            $table->enum('sent_status', ['0', '1'])->default('0')->comment('0 -> Pending, 1 -> Sent');
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
        Schema::dropIfExists('challenge_announcements');
    }
};
