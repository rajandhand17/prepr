<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('labs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('language')->default('en');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('duration_id')->nullable();
            $table->unsignedBigInteger('level_id')->nullable();
            $table->enum('type', ['0', '1', '2', '3', '4'])->comment('0-> assess, 1-> onboard, 2-> engage, 3-> grow, 4-> na ')->default('4');
            $table->string('slug')->nullable();
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->enum('privacy', ['0', '1'])->comment('0->no,1->yes')->default('0');
            $table->string('media_type')->default('image')->nullable();
            $table->text('media')->nullable();
            $table->enum('status', ['0', '1', '2'])->default('0')->comment('0-> draft, 1-> published, 2-> archive');
            $table->integer('total_share')->nullable();
            $table->enum('is_auto_created', ['0', '1'])->comment('0-> no, 1-> yes')->default('0');
            $table->enum('is_resource_sequential', ['0', '1'])->comment('0-> no,1-> yes')->default('0');
            $table->enum('is_sequential', ['0', '1'])->comment('0-> no,1-> yes')->default('0');
            $table->enum('is_achievement_enabled', ['0', '1'])->comment('0-> no,1-> yes')->default('0');
            $table->enum('is_notification_enabled', ['0', '1'])->comment('0-> no,1-> yes')->default('0');
            $table->enum('is_verified', ['0', '1'])->default('0')->comment('0-> not-verified ,1-> verified');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('labs');
    }
};
