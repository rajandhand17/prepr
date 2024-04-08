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
        Schema::table('resource_modules', function (Blueprint $table) {
            $table->unsignedBigInteger('go1_course_id')->nullable();
            $table->json('go1_metadata')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resource_modules', function (Blueprint $table) {
            $table->dropColumn('go1_course_id');
            $table->dropColumn('go1_metadata');
        });
    }
};
