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
            $table->dropColumn('follow_unfollow');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lab_social_activities', function (Blueprint $table) {
            $table->enum('follow_unfollow', ['0', '1', '2'])->default('0')->comment('0->no-activity, 1=>follow,2=>unfollow');
        });
    }
};
