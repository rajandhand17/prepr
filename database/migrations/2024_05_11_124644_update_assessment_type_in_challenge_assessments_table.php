<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE challenge_assessments MODIFY COLUMN assessment_type ENUM('0', '1', '2', '3') COMMENT '0->none assessment, 1->open assessment, 2->closed assessment, 3->ai open assessment'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE challenge_assessments MODIFY COLUMN assessment_type ENUM('0', '1', '2') COMMENT '0->none assessment, 1->open assessment, 2->closed assessment'");
    }
};
