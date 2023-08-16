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
        Schema::table('lab_social_activities', function (Blueprint $table) {
            $table->enum('like_dislike', ['0', '1', '2'])->default('0')->comment('0->no-activity, 1=>like,2=>dislike')->after('lab_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lab_social_activities', function (Blueprint $table) {
            $table->dropColumn('like_dislike');
        });
    }
};
