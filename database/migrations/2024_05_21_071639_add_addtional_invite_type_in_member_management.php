<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('member_management', function (Blueprint $table) {
            DB::statement("ALTER TABLE member_management MODIFY COLUMN invite_type ENUM('0', '1', '2', '3', '4') COMMENT '0-> email, 1-> network, 2-> join_request, 3-> csv, 4->unified'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_management', function (Blueprint $table) {
            DB::statement("ALTER TABLE member_management MODIFY COLUMN invite_type ENUM('0', '1', '2', '3') COMMENT '0-> email, 1-> network, 2-> join_request, 3-> csv'");
        });
    }
};
