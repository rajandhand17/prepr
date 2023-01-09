<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flexible_announcements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->nullable();
            $table->integer('challenge_id')->nullable();
            $table->string('custom_date_id')->nullable();
            $table->enum('sent_status', ['email', 'push', 'inbox'])->nullable();
            $table->string('title', 1000)->nullable();
            $table->text('body')->nullable();
            $table->enum('schedule_status', ['immediately', 'custome'])->nullable();
            $table->enum('is_completed', ['0', '1'])->nullable();
            $table->integer('announcement_number')->nullable();
            $table->string('announcement_schedule')->nullable();
            $table->string('announcement_schedule_time')->nullable();
            $table->string('created_at')->nullable();
            $table->string('updated_at')->nullable();
            $table->string('deleted_at')->nullable();
            $table->index(['user_id']);
            $table->index(['challenge_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flexible_announcements');
    }
};
