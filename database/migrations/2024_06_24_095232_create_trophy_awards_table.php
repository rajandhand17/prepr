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
        Schema::create('trophy_awards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('parent_id')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('issue_trophy_date')->nullable();
            $table->string('expiration_date')->nullable();
            $table->integer('no_of_times_issued')->nullable();
            $table->text('image')->nullable();
            $table->enum('status', ['active', 'inactive'])->nullable();
            $table->bigInteger('user_id');
            $table->integer('points_gained')->nullable();
            $table->string('trophy_code_id', 255)->nullable();
            $table->string('created_at');
            $table->string('updated_at');
            $table->string('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trophy_awards');
    }
};
