<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('project_member_management', function (Blueprint $table) {
            $table->enum('auto_invite', ['0', '1', '2'])->comment('0 -> No, 1 -> Yes, 2 -> NA')->default('0')->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_member_management', function (Blueprint $table) {
            $table->dropColumn('auto_invite');
        });
    }
};
