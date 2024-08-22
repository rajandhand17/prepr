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
        Schema::table('projects', function (Blueprint $table) {
            $table->enum('is_submitted', ['0', '1', '2'])->default('0')->comment('Project Submission Status, 0 -> Not Submitted & 1 -> Submitted & 2-> Late Submitted')
            ->change();
            $table->text('late_submission_reason')->after('is_submitted')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->enum('is_submitted', ['0', '1', '2'])->default('0')->comment('Project Submission Status, 0 -> Not Submitted & 1 -> Submitted & 2-> Late Submitted')
            ->change();
            $table->string('late_submission_reason', 1000)->nullable();
        });
    }
};
