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
        Schema::create('friends', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('reference_id');
            $table->enum('status', ['0', '1', '2'])->default('0')->comment('0 for pending, 1 for accepted,2 for rejected');
            $table->enum('user_follow', ['0', '1', '2', '3'])->default('0')->comment('0 for none, 1 for request,2 for follow,3 for unfollow');
            $table->enum('reference_follow', ['0', '1', '2', '3'])->default('0')->comment('0 for none, 1 for request,2 for follow, 3 for unfollow');
            $table->enum('newsfeed', ['0', '1', '2'])->default('1')->comment('0 for pending, 1 for allowed all,2 for not allowed');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reference_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('friends');
    }
};
