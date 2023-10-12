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
            $table->enum('allow_submit_project', ['0', '1'])->default('0')->after('min_achievement_counts')->comment('0->no,1->yes');
            $table->enum('requirement_program', ['0', '1'])->default('0')->after('allow_submit_project')->comment('0->no,1->yes');
            $table->enum('complete_education_program', ['0', '1'])->default('0')->after('requirement_program')->comment('0->no,1->yes');
            $table->enum('complete_experience', ['0', '1'])->default('0')->after('complete_education_program')->comment('0->no,1->yes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('challenge_requirements', function (Blueprint $table) {
            $table->dropColumn('allow_submit_project');
            $table->dropColumn('requirement_program');
            $table->dropColumn('complete_education_program');
            $table->dropColumn('complete_experience');
        });
    }
};
