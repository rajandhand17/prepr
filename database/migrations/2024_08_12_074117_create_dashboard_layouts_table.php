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
        Schema::create('dashboard_layouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('dashboard_type', ['0', '1', '2'])->comment('0 -> User, 1 -> Lab, 2 -> Organization')->nullable();
            $table->enum('card_type', ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'])->comment('0 -> reports, 1 -> deadlines, 2 -> leaderboard, 3 -> my-challenges, 4 -> my-labs, 5 -> my-projects, 6 -> my-resources, 7 -> my-organizations, 8 -> subscription, 9 -> inbox-friends, 10 -> recommendations, 11 -> continue-left, 12 -> achievement')->nullable();
            $table->integer('position_x')->comment('card on the X-axis')->nullable();
            $table->integer('position_y')->comment('card on the Y-axis')->nullable();
            $table->enum('is_active', ['0', '1'])->comment('0 -> active, 1 -> inactive')->default('0');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard_layouts');
    }
};
