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
        Schema::table('challenge_requirements', function (Blueprint $table) {
            $table->integer('max_project_associate')->after('max_project_submission')->comment('maximum limit to project associated')->nullable();
            $table->text('additional_requirements')->after('min_achievement_counts')->comment('Addtional requirements if any')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('challenge_requirements', function (Blueprint $table) {
            $table->dropColumn('max_project_associate');
            $table->dropColumn('additional_requirements');
        });
    }
};
