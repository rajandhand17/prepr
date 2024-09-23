<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('unified_connections', function (Blueprint $table) {
            DB::statement("ALTER TABLE unified_connections MODIFY COLUMN usage_type ENUM('0', '1', '2', '3') COMMENT '0 -> organization_member_invite, 1 -> lab_member_invite, 2-> challenge_member_invite, 3 => lab_program_member_invite'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unified_connections', function (Blueprint $table) {
            DB::statement("ALTER TABLE unified_connections MODIFY COLUMN usage_type ENUM('0', '1', '2') COMMENT '0 -> organization_member_invite, 1 -> lab_member_invite, 2-> challenge_member_invite'");
        });
    }
};
