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
        Schema::create('resource_group_ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resource_group_id');
            $table->bigInteger('user_id');
            $table->enum('rating', ['0', '1', '2', '3', '4', '5'])->comment('1->one star, 2->two star, 3->three star, 4->four star, 5->five star')->default('0');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('resource_group_id')->references('id')->on('resource_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_group_ratings');
    }
};
