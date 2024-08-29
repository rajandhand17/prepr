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
        Schema::create('lab_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('user_id');
            $table->longText('activity')->comment('activity updates in lab')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('module_id')->references('id')->on('labs')->onDelete('cascade');
            $table->index(['module_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lab_histories', function (Blueprint $table) {
            $table->dropIndex(['module_id', 'deleted_at']);
            $table->dropForeign(['module_id']);
        });

        Schema::dropIfExists('lab_histories');
    }
};
