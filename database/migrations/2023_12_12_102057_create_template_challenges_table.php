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
        Schema::create('challenge_templates', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('language')->default('en');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('duration_id')->nullable();
            $table->unsignedBigInteger('level_id')->nullable();
            $table->string('slug')->nullable();
            $table->string('title')->nullable();
            $table->enum('description_type', ['0', '1'])->comment('0->text,1->scorm')->default('0');
            $table->longText('description')->nullable();
            $table->enum('privacy', ['0', '1'])->comment('0->no,1->yes')->default('0');
            $table->string('media_type')->default('image')->nullable();
            $table->text('media')->nullable();
            $table->enum('status', ['0', '1'])->default('0')->comment('0-> draft, 1-> published');
            $table->text('source_link')->nullable();
            $table->longText('agreement')->nullable();
            $table->enum('is_notification_enabled', ['0', '1'])->comment('0->no,1->yes & let users know if any updates are made')->default('0');
            $table->enum('project_privacy', ['0', '1'])->comment('0->no,1->yes')->default('0');
            $table->enum('is_pre_built', ['0', '1'])->comment('0->no,1->yes')->default('0');
            $table->enum('is_open', ['0', '1', '2'])->comment('0 -> open,1 -> close, 3 -> completed')->default('0');
            $table->enum('is_auto_created', ['0', '1'])->comment('0->no,1->yes')->default('0');
            $table->enum('allow_winner_change', ['0', '1'])->comment('Winner change is allowed, 0->yes ,1->no')->default('0');
            $table->string('winner_select_date', 255)->nullable()->comment('Date of start challenge winner selection');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('duration_id')->references('id')->on('durations')->onDelete('cascade');
            $table->foreign('level_id')->references('id')->on('levels')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_templates');
    }
};
